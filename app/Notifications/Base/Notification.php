<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications\Base;

use App\Models\Base\INotifiable;
use App\Models\Base\INotifier;
use App\Utils\ClientSettings\Capture;
use App\Vendors\Illuminate\Support\Facades\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Notification extends NowNotification implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Capture;

    public const NAME = 'notification';

    public function __construct(INotifier $notifier = null)
    {
        parent::__construct($notifier);

        if (!$this->independentClientId()) {
            $this->settingsCapture();
        }
    }

    protected function resolveData($via, INotifiable $notifiable, $dataCallback)
    {
        if (App::notRunningFromRequest() && !$this->independentClientId()) {
            return $this->settingsTemporary(function () use ($via, $notifiable, $dataCallback) {
                return parent::resolveData($via, $notifiable, $dataCallback);
            });
        }
        return parent::resolveData($via, $notifiable, $dataCallback);
    }
}
