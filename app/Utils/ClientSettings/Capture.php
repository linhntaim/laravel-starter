<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ClientSettings;

trait Capture
{
    public $locale;

    protected $settings;

    public function settingsCapture()
    {
        $this->settings = Facade::capture();
        $this->locale = $this->settings->getLocale();
        return $this;
    }

    public function settingsTemporary(callable $callback)
    {
        if (!empty($this->settings)) {
            return Facade::temporary($this->settings, $callback);
        }
        return $callback();
    }
}
