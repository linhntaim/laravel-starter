<?php

namespace App\Utils\ClientApp;

trait BaseTrait
{
    protected $clientAppName;
    protected $clientAppUrl;

    protected function getClientAppId()
    {
        return null;
    }

    private function createClientApp()
    {
        $clientAppHelper = Helper::fetchInstanceByClient($this->getClientAppId());
        $this->clientAppName = $clientAppHelper->getName();
        $this->clientAppUrl = $clientAppHelper->getUrl();
    }

    private function destroyClientApp()
    {
        Helper::getOut();
    }

    public function getClientAppName()
    {
        return $this->clientAppName;
    }

    public function getClientAppUrl()
    {
        return $this->clientAppUrl;
    }
}
