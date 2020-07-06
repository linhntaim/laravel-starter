<?php

namespace App\Notifications\Base;

use App\Models\Base\IUser;
use App\Utils\ClientSettings\Capture;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Notification extends NowNotification implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Capture;

    const NAME = 'notification';

    protected function resolveData($via, IUser $notifiable, $dataCallback)
    {
        return $this->settingsTemporary(function () use ($via, $notifiable, $dataCallback) {
            return parent::resolveData($via, $notifiable, $dataCallback);
        });
    }
}
