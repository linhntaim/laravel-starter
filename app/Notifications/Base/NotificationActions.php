<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Notifications\Base;

class NotificationActions
{
    public static function actionGo($screen, $arguments = null, $options = null, $position = null)
    {
        return [
            'name' => 'go',
            'screen' => $screen,
            'arguments' => $arguments, // params
            'options' => $options, // query string
            'position' => $position, // hash
        ];
    }

    public static function actionUrl($url)
    {
        return [
            'name' => 'url',
            'url' => $url,
        ];
    }
}
