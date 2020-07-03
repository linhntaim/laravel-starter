<?php

namespace App\Utils\ClientSettings;

use App\Utils\ClientSettings\Facade;

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
