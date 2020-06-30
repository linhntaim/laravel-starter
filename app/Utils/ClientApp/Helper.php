<?php

namespace App\Utils\ClientApp;

use App\Utils\BlockedSingleton;
use App\Utils\ConfigHelper;

class Helper
{
    use BlockedSingleton;

    public static function fetchInstanceByClient($clientId)
    {
        return static::getInstance(true, function (Helper $helper) use ($clientId) {
            return $helper->setByClient($clientId);
        });
    }

    public static function fetchInstanceByRequest()
    {
        return static::getInstance(true, function (Helper $helper) {
            return $helper->setByRequestHeader();
        });
    }

    protected $name;
    protected $url;

    protected function onCreating()
    {
        $this->name = ConfigHelper::getAppName();
        $this->url = ConfigHelper::getAppUrl();
    }

    public function setByRequestHeader()
    {
        if (request()->hasHeader('Application')) {
            if ($app = request()->header('Application')) {
                $app = json_decode($app, true);
                if ($app !== false && !empty($app)) {
                    $this->name = $app['name'];
                    $this->url = $app['url'];
                }
            }
        }

        return $this;
    }

    public function setByClient($clientId)
    {
        if (!empty($clientId)) {
            $app = ConfigHelper::getClient($clientId);
            $this->name = $app['name'];
            $this->url = $app['url'];
        }

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
