<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

class CurrentScreen
{
    protected static $screen;

    public static function set($screen)
    {
        static::$screen = $screen;
    }

    public static function get()
    {
        return static::$screen;
    }

    public static function getAsStack()
    {
        return [static::get()];
    }

    public static function getName()
    {
        return isset(static::$screen['name']) ? static::$screen['name'] : '';
    }

    public static function getClient()
    {
        return isset(static::$screen['client']) ? static::$screen['client'] : '';
    }
}
