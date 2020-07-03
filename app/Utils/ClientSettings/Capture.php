<?php

namespace App\Utils\ClientSettings;

use App\Utils\Facades\ClientSettings;

trait Capture
{
    public $locale;

    protected $settings;

    public function settingsCapture()
    {
        $this->settings = ClientSettings::capture();
        $this->locale = $this->settings->getLocale();
        return $this;
    }

    public function settingsTemporary(callable $callback)
    {
        if (!empty($this->settings)) {
            return ClientSettings::temporary($this->settings, $callback);
        }
        return $callback();
    }
}
