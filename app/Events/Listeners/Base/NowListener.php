<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events\Listeners\Base;

use App\Utils\ClassTrait;
use App\Utils\ClientSettings\Facade;
use App\Utils\ClientSettings\Traits\IndependentClientTrait;
use App\Utils\Database\Transaction\TransactionTrait;
use App\Utils\Mail\MailHelper;
use App\Utils\Mail\TemplateMailable;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class NowListener
{
    use ClassTrait, IndependentClientTrait, TransactionTrait;

    protected $transactionUsed = false;

    protected static function __transCurrentModule()
    {
        return 'listener';
    }

    public function start()
    {
        Log::info(sprintf('%s listening...', static::class));
        return $this;
    }

    public function end()
    {
        Log::info(sprintf('%s listened!', static::class));
        return $this;
    }

    public function fails()
    {
        Log::info(sprintf('%s failed!', static::class));
        return $this;
    }

    public function handle($event)
    {
        if ($this->transactionUsed) {
            $this->transactionStart();
        }
        $this->independentClientApply();
        try {
//        $this->start();
            $this->go($event);
//            $this->end();
            if ($this->transactionUsed) {
                $this->transactionComplete();
            }
        }
        catch (Throwable $e) {
            if (!($this instanceof Listener)) {
                if ($this->transactionUsed) {
                    $this->transactionStop();
                }
                $this->failed($event, $e);
            }
            throw $e;
        }
    }

    protected abstract function go($event);

    public function failed($event, Throwable $e)
    {
        $this->fails();
    }

    protected function getMailNow($event)
    {
        return true;
    }

    protected function getMailTemplate($event)
    {
        return 'password_reset';
    }

    protected function getMailSubject($event)
    {
        return $this->__transMailWithModule('mail_subject', [
            'app_name' => Facade::getAppName(),
        ]);
    }

    protected function getMailParams($event)
    {
        return [
            TemplateMailable::EMAIL_SUBJECT => $this->getMailSubject($event),
        ];
    }

    protected function sendMail($event, $templateLocalized = true, $templateLocale = null)
    {
        return $this->getMailNow($event) ? MailHelper::sendNowWithTemplate(
            $this->getMailTemplate($event),
            $this->getMailParams($event),
            $templateLocalized,
            $templateLocale
        ) : MailHelper::sendWithTemplate(
            $this->getMailTemplate($event),
            $this->getMailParams($event),
            $templateLocalized,
            $templateLocale
        );
    }
}
