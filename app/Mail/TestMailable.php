<?php

namespace App\Mail;

use App\Mail\Base\TemplateMailable;
use App\Utils\ConfigHelper;

class TestMailable extends TemplateMailable
{
    public $emailView = 'test';

    public function __construct($subject = 'Tested', $emailView = 'test')
    {
        parent::__construct();

        $mail = ConfigHelper::getTestedMail();
        $this
            ->to(
                is_array($mail) ? $mail['address'] : $mail,
                is_array($mail) && isset($mail['name']) ? $mail['name'] : null
            )
            ->subject($subject)
            ->setEmailView($emailView);
    }
}