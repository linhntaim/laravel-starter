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
        return $this->setLocale($this->settings->getLocale());
    }

    public function settingsTemporary(callable $callback)
    {
        if (!empty($this->settings)) {
            return Facade::temporary($this->settings, $callback);
        }
        return $callback();
    }

    public function setLocale(string $locale)
    {
        $this->locale = $locale;
        return $this;
    }
}
