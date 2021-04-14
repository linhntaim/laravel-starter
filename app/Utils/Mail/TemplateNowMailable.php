<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Mail;

use App\Exceptions\AppException;
use App\Utils\ClassTrait;
use App\Utils\ConfigHelper;
use App\Utils\ClientSettings\Facade;
use App\Utils\RateLimiterTrait;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Swift_DependencyContainer;

class TemplateNowMailable extends Mailable
{
    use ClassTrait, RateLimiterTrait;

    const DEFAULT_CHARSET = 'UTF-8';

    const HTML_CHARSETS = [
        'sjis' => 'SHIFT_JS',
        'sjis-win' => 'SHIFT_JS',
        'sjis-mac' => 'SHIFT_JS',
        'macjapanese' => 'SHIFT_JS',
        'sjis-mobile#docomo' => 'SHIFT_JS',
        'sjis-mobile#kddi' => 'SHIFT_JS',
        'sjis-mobile#softbank' => 'SHIFT_JS',
        'jis' => 'ISO-2022-JP',
        'jis-win' => 'ISO-2022-JP',
        'iso-2022-jp-ms' => 'ISO-2022-JP',
        'iso-2022-jp-mobile#kddi' => 'ISO-2022-JP',
    ];

    const EMAIL_FROM = 'x_email_from';
    const EMAIL_FROM_NAME = 'x_email_from_name';
    const EMAIL_TO = 'x_email_to';
    const EMAIL_TO_NAME = 'x_email_to_name';
    const EMAIL_SUBJECT = 'x_email_subject';

    /**
     * @var array
     */
    protected $charset;

    protected $htmlCharset;

    protected $templateName;

    protected $templateParams;

    protected $templateLocalized;

    protected $templateNamespace;

    public function __construct($templateName, array $templateParams = [], $templateLocalized = true, $templateLocale = null, $templateNamespace = null, $charset = null)
    {
        $this->templateName = $templateName;
        $this->templateParams = $templateParams;
        $this->templateLocalized = $templateLocalized;
        $this->templateNamespace = $templateNamespace;

        $this->locale = $templateLocale ? $templateLocale : Facade::getLocale();
        $this->parseCharset(is_null($charset) ? ConfigHelper::get('emails.send_charset') : $charset);
    }

    /**
     * @param string|array $charset
     */
    protected function parseCharset($charset)
    {
        if (is_string($charset)) {
            $this->charset = $this->parseCharsetString($charset);
        } elseif (is_array($charset)) {
            $this->charset = [
                'header' => isset($charset['header']) ?
                    $this->parseCharsetString($charset['header']) : $this->defaultCharset(),
                'body' => isset($charset['body']) ?
                    $this->parseCharsetString($charset['body']) : $this->defaultCharset(),
            ];
        } else {
            $this->charset = $this->defaultCharset();
        }
    }

    protected function defaultCharset()
    {
        return [
            'default' => static::DEFAULT_CHARSET,
            'locales' => [],
        ];
    }

    /**
     * @param string $charsetString Sample: "UTF-8,ja:ISO-2022-JP"
     * @return array
     */
    protected function parseCharsetString($charsetString)
    {
        $charset = $this->defaultCharset();
        foreach (explode(',', $charsetString) as $localeCharset) {
            $localeCharsets = explode(':', $localeCharset, 2);
            if (!isset($localeCharsets[1])) {
                if (!empty($localeCharsets[0])) {
                    $charset['default'] = $localeCharsets[0];
                }
            } else {
                $charset['locales'][$localeCharsets[0]] = $localeCharsets[1];
            }
        }
        return $charset;
    }

    protected function usingDefaultCharset()
    {
        return ($this->usingOneCharset()
                && strtolower($this->getCharset($this->charset)) == strtolower(TemplateNowMailable::DEFAULT_CHARSET))
            || (!$this->usingOneCharset()
                && strtolower($this->getCharset($this->charset['header'])) == strtolower(TemplateNowMailable::DEFAULT_CHARSET)
                && strtolower($this->getCharset($this->charset['body'])) == strtolower(TemplateNowMailable::DEFAULT_CHARSET));
    }

    protected function usingOneCharset()
    {
        return isset($this->charset['default']);
    }

    protected function getCharset($charset)
    {
        return isset($charset['locales'][$this->locale]) ? $charset['locales'][$this->locale] : $charset['default'];
    }

    protected function headerCharset()
    {
        return $this->getCharset($this->usingOneCharset() ? $this->charset : $this->charset['header']);
    }

    protected function bodyCharset()
    {
        return $this->getCharset($this->usingOneCharset() ? $this->charset : $this->charset['body']);
    }

    protected function htmlCharset()
    {
        if (is_null($this->htmlCharset)) {
            $this->htmlCharset = (function () {
                $bodyCharset = $this->bodyCharset();
                $lowerBodyCharset = strtolower($bodyCharset);
                return isset(static::HTML_CHARSETS[$lowerBodyCharset]) ? static::HTML_CHARSETS[$lowerBodyCharset] : $bodyCharset;
            })();
        }
        return $this->htmlCharset;
    }

    protected function getTemplatePath()
    {
        return sprintf('%semails.%s%s',
            $this->templateNamespace ? $this->templateNamespace . '::' : '',
            $this->templateName,
            ($this->templateLocalized ? '.' . $this->locale : '')
        );
    }

    protected function getTemplateParams()
    {
        return array_merge($this->templateParams, [
            'locale' => $this->locale,
            'charset' => $this->htmlCharset(),
        ]);
    }

    public function build()
    {
        if ($this->usingDefaultCharset()) {
            Swift_DependencyContainer::getInstance()
                ->register('mime.qpheaderencoder')
                ->asNewInstanceOf('Swift_Mime_HeaderEncoder_QpHeaderEncoder')
                ->withDependencies(['mime.charstream']);
        } else {
            Swift_DependencyContainer::getInstance()
                ->register('mime.qpheaderencoder')
                ->asAliasOf('mime.base64headerencoder');
            $this->callbacks[] = function (\Swift_Message $message) {
                $message->setCharset($this->bodyCharset());
                $message->getHeaders()->setCharset($this->headerCharset());
            };
        }

        if (isset($this->templateParams[static::EMAIL_FROM])) {
            if (empty($this->templateParams[static::EMAIL_FROM])) {
                throw new AppException('From email has been not set');
            }
            if (isset($this->templateParams[static::EMAIL_FROM_NAME])) {
                $this->from($this->templateParams[static::EMAIL_FROM], $this->templateParams[static::EMAIL_FROM_NAME]);
            } else {
                $this->from($this->templateParams[static::EMAIL_FROM]);
            }
        } else {
            $noReplyMail = ConfigHelper::getNoReplyMail();
            if (empty($noReplyMail['address'])) {
                throw new AppException('No-reply email has been not set');
            }
            $this->from($noReplyMail['address'], $noReplyMail['name']);
        }

        $emailTested = ConfigHelper::getTestedMail();
        if ($emailTested['used']) {
            if (empty($emailTested['address'])) {
                throw new AppException('Tested email has been not set');
            }
            $this->to($emailTested['address'], $emailTested['name']);
        } else {
            if (empty($this->templateParams[static::EMAIL_TO])) {
                throw new AppException('To email has been not set');
            }
            if (isset($this->templateParams[static::EMAIL_TO_NAME])) {
                $this->to($this->templateParams[static::EMAIL_TO], $this->templateParams[static::EMAIL_TO_NAME]);
            } else {
                $this->to($this->templateParams[static::EMAIL_TO]);
            }
        }

        $this->subject(
            isset($this->templateParams[static::EMAIL_SUBJECT]) ?
                $this->templateParams[static::EMAIL_SUBJECT]
                : $this->__transWithModule('default_subject', 'label', ['app_name' => Facade::getAppName()])
        );

        $this->view($this->getTemplatePath(), $this->getTemplateParams());
    }

    public function send($mailer)
    {
        $maxAttempts = ConfigHelper::get('emails.send_rate_per_second');
        if ($maxAttempts) {
            $this->getLimiter();

            $key = ConfigHelper::get('emails.send_rate_key');
            if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
                Log::info(sprintf('Delay mailing: %s - %s - %s', static::class, $this->templateName, json_encode($this->templateParams)));
                sleep(ConfigHelper::get('emails.send_rate_wait_for_seconds'));
                $this->send($mailer);
                return;
            }

            $this->limiter->hit($key, 1);
        }

        parent::send($mailer);
    }
}
