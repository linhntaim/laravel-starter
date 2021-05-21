<?php

namespace App\Mail\Base;

use App\Utils\ConfigHelper;
use App\Utils\ReportExceptionTrait;
use Illuminate\Support\Facades\Mail;
use Throwable;

trait MailTrait
{
    use ReportExceptionTrait;

    public function mail(NowMailable $mailable, $exceptionSkipped = false, $message = null)
    {
        if (ConfigHelper::get('emails.send_off')) {
            return true;
        }

        try {
            Mail::send($mailable);
            return true;
        }
        catch (Throwable $e) {
            if ($exceptionSkipped) {
                $this->reportException($e);
            }
            else {
                throw MailException::from(
                    $e,
                    $message
                );
            }
        }
        return false;
    }
}