<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events;

class MailTestingEvent extends Event
{
    protected $subject;

    protected $templatePath;

    public function __construct($subject = 'Tested', $templatePath = 'test')
    {
        $this->subject = $subject;
        $this->templatePath = $templatePath;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }
}
