<?php

namespace App\Utils\Screen;

use App\Http\Requests\Request;
use App\Utils\ConfigHelper;

class Manager
{
    /**
     * @var array|null
     */
    protected $screen;

    public function setScreen($screen)
    {
        $this->screen = $screen;
        return $this;
    }

    public function getScreen()
    {
        return $this->screen;
    }

    public function getScreens()
    {
        return is_null($this->screen) ? null : [$this->screen];
    }

    public function getScreenName()
    {
        return is_null($this->screen) || !isset($this->screen['name']) ? null : $this->screen['name'];
    }

    public function fetchFromRequestHeader(Request $request)
    {
        if ($request->ifHeaderJson(ConfigHelper::get('client.headers.screen'), $headerValue)) {
            return $this->setScreen($headerValue);
        }
        return $this;
    }
}
