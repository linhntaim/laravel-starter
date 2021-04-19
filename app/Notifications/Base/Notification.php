<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications\Base;

use App\Models\Base\IUser;
use App\Utils\ClientSettings\Capture;
use App\Vendors\Illuminate\Support\Facades\App;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Notification extends NowNotification implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Capture;

    const NAME = 'notification';

    public function __construct(IUser $notifier = null)
    {
        parent::__construct($notifier);

        if (!$this->independentClientId()) {
            $this->settingsCapture();
        }
    }

    protected function resolveData($via, IUser $notifiable, $dataCallback)
    {
        if (App::notRunningFromRequest() && !$this->independentClientId()) {
            return $this->settingsTemporary(function () use ($via, $notifiable, $dataCallback) {
                return parent::resolveData($via, $notifiable, $dataCallback);
            });
        }
        return parent::resolveData($via, $notifiable, $dataCallback);
    }
}
