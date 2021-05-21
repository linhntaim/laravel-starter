<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Events;

class TestMailEvent extends Event
{
    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $view;

    public function __construct($subject = 'Tested', $view = 'test')
    {
        $this->subject = $subject;
        $this->view = $view;
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
    public function getView()
    {
        return $this->view;
    }
}
