<?php

namespace App\Mail\Base;

use App\Exceptions\AppException;
use App\Exceptions\Exception;
use App\Utils\ClassTrait;
use App\Utils\ClientSettings\Facade;
use App\Utils\ConfigHelper;
use App\Utils\RateLimiterTrait;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Swift_DependencyContainer;
use Swift_Message;
use Throwable;

abstract class NowMailable extends Mailable
{
    use ClassTrait, RateLimiterTrait;

    public const DEFAULT_CHARSET = 'UTF-8';
    public const HTML_CHARSETS = [
        'sjis' => 'SHIFT_JIS',
        'sjis-win' => 'SHIFT_JIS',
        'sjis-mac' => 'SHIFT_JIS',
        'macjapanese' => 'SHIFT_JIS',
        'sjis-mobile#docomo' => 'SHIFT_JIS',
        'sjis-mobile#kddi' => 'SHIFT_JIS',
        'sjis-mobile#softbank' => 'SHIFT_JIS',
        'jis' => 'ISO-2022-JP',
        'jis-win' => 'ISO-2022-JP',
        'iso-2022-jp-ms' => 'ISO-2022-JP',
        'iso-2022-jp-mobile#kddi' => 'ISO-2022-JP',
    ];

    /**
     * @var array
     */
    protected $charset;

    /**
     * @var string
     */
    protected $htmlCharset;

    /**
     * @var int
     */
    protected $sendMaxAttempts;

    /**
     * @var string
     */
    protected $sendAttemptCacheKey;

    /**
     * @var int
     */
    protected $sendAttemptDelay;

    public function __construct()
    {
        $this->locale(Facade::getLocale())
            ->setCharset(ConfigHelper::get('emails.send_charset'));

        $this->sendMaxAttempts = ConfigHelper::get('emails.send_rate_per_second');
        $this->sendAttemptCacheKey = ConfigHelper::get('emails.send_rate_key');
        $this->sendAttemptDelay = ConfigHelper::get('emails.send_rate_wait_for_seconds');
    }

    #region Charset
    protected function defaultCharset()
    {
        return [
            'default' => static::DEFAULT_CHARSET,
            'locales' => [],
        ];
    }

    /**
     * @param string|array $charset
     * @return static
     */
    public function setCharset($charset)
    {
        $this->charset = $this->parseCharset($charset);
        $this->htmlCharset = (function () {
            $bodyCharset = $this->bodyCharset();
            return static::HTML_CHARSETS[strtolower($bodyCharset)] ?? $bodyCharset;
        })();
        return $this;
    }

    /**
     * @param string|array $charset
     */
    protected function parseCharset($charset)
    {
        if (is_string($charset)) {
            return $this->parseCharsetString($charset);
        }
        if (is_array($charset)) {
            return [
                'header' => isset($charset['header']) ?
                    $this->parseCharsetString($charset['header']) : $this->defaultCharset(),
                'body' => isset($charset['body']) ?
                    $this->parseCharsetString($charset['body']) : $this->defaultCharset(),
            ];
        }
        return $this->defaultCharset();
    }

    /**
     * @param string $charsetString Sample: "UTF-8,ja:ISO-2022-JP"
     * @return array
     */
    protected function parseCharsetString(string $charsetString)
    {
        $charset = $this->defaultCharset();
        foreach (explode(',', $charsetString) as $localeCharset) {
            $localeCharsets = explode(':', $localeCharset, 2);
            if (!isset($localeCharsets[1])) {
                if (!empty($localeCharsets[0])) {
                    $charset['default'] = $localeCharsets[0];
                }
            }
            else {
                $charset['locales'][$localeCharsets[0]] = $localeCharsets[1];
            }
        }
        return $charset;
    }

    protected function usingDefaultCharset()
    {
        if ($this->usingOneCharset()) {
            return strtolower($this->getCharset()) == strtolower(TemplateNowMailable::DEFAULT_CHARSET);
        }
        return strtolower($this->headerCharset()) == strtolower(TemplateNowMailable::DEFAULT_CHARSET)
            && strtolower($this->bodyCharset()) == strtolower(TemplateNowMailable::DEFAULT_CHARSET);
    }

    protected function usingOneCharset()
    {
        return isset($this->charset['default']);
    }

    protected function getCharset($part = null)
    {
        $charset = $this->usingOneCharset() || is_null($part) ?
            $this->charset : $this->charset[$part];
        return $charset['locales'][$this->locale] ?? $charset['default'];
    }

    protected function headerCharset()
    {
        return $this->getCharset('header');
    }

    protected function bodyCharset()
    {
        return $this->getCharset('body');
    }

    #endregion

    #region Build
    public function build()
    {
        return $this->prepareCharset()
            ->prepareFrom()
            ->prepareTo();
    }

    protected function prepareCharset()
    {
        if ($this->usingDefaultCharset()) {
            Swift_DependencyContainer::getInstance()
                ->register('mime.qpheaderencoder')
                ->asNewInstanceOf('Swift_Mime_HeaderEncoder_QpHeaderEncoder')
                ->withDependencies(['mime.charstream']);
        }
        else {
            Swift_DependencyContainer::getInstance()
                ->register('mime.qpheaderencoder')
                ->asAliasOf('mime.base64headerencoder');
            $this->callbacks[] = function (Swift_Message $message) {
                $message->setCharset($this->bodyCharset());
                $message->getHeaders()->setCharset($this->headerCharset());
            };
        }
        return $this;
    }

    protected function prepareFrom()
    {
        if (empty($this->from)) {
            $mailAddress = MailAddress::from(ConfigHelper::getNoReplyMail());
            $this->from($mailAddress->address, $mailAddress->name);
        }
        return $this;
    }

    protected function prepareTo()
    {
        $testedMail = ConfigHelper::getTestedMail();
        if ($testedMail['used']) {
            $mailAddress = MailAddress::from($testedMail);
            return $this->to($mailAddress->address, $mailAddress->name);
        }
        if (empty($this->to)) {
            throw new AppException('To e-mail has been not set.');
        }
        return $this;
    }
    #endregion

    #region Send
    public function send($mailer)
    {
        if ($this->reachSendingLimit()) {
            $this->sendOnDelay($mailer);
        }
        else {
            parent::send($mailer);
        }
    }

    protected function sendOnDelay($mailer)
    {
        Log::warning(sprintf('[%s] delaying', static::class));

        sleep($this->sendAttemptDelay);

        $this->send($mailer);
    }

    protected function reachSendingLimit()
    {
        if ($this->sendMaxAttempts) {
            $this->getLimiter();

            if ($this->limiter->tooManyAttempts($this->sendAttemptCacheKey, $this->sendMaxAttempts)) {
                return true;
            }

            $this->limiter->hit($this->sendAttemptCacheKey, 1);
        }
        return false;
    }
    #endregion

    public function failed(Throwable $e)
    {
    }
}