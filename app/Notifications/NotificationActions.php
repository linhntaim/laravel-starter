<?php

namespace App\Notifications;

class NotificationActions
{
    protected static function actionGo($screen, $arguments = null, $options = null, $position = null)
    {
        return [
            'name' => 'go',
            'screen' => $screen,
            'arguments' => $arguments, // params
            'options' => $options, // query string
            'position' => $position, // hash
        ];
    }

    protected static function actionUrl($url)
    {
        return [
            'name' => 'url',
            'url' => $url,
        ];
    }
}
