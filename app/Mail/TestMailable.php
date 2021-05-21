<?php

namespace App\Mail;

use App\Mail\Base\MailAddress;
use App\Mail\Base\TemplateMailable;
use App\Utils\ConfigHelper;

class TestMailable extends TemplateMailable
{
    public $emailView = 'test';

    public function __construct($subject = 'Tested', $emailView = 'test')
    {
        parent::__construct();

        $mail = MailAddress::from(ConfigHelper::getTestedMail(), 'Test e-mail has not been configured.');
        $this->to($mail->address, $mail->name)
            ->subject($subject)
            ->setEmailLocalized(false)
            ->setEmailView($emailView);
    }
}