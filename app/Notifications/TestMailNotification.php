<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications;

use App\Mail\TestMailable;
use App\Models\Base\INotifiable;
use App\Models\Base\INotifier;
use App\Notifications\Base\NowNotification;

class TestMailNotification extends NowNotification
{
    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $view;

    public function __construct($subject = 'Tested', $view = 'test', INotifier $notifier = null)
    {
        parent::__construct($notifier);

        $this->subject = $subject;
        $this->view = $view;
    }

    public function shouldMail()
    {
        return true;
    }

    protected function getMailable(INotifiable $notifiable)
    {
        return new TestMailable($this->subject, $this->view);
    }
}
