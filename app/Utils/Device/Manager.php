<?php

namespace App\Utils\Device;

use App\Http\Requests\Request;
use App\ModelRepositories\DeviceRepository;
use App\Models\Device;
use App\Utils\ClientSettings\Facade as ClientSettingsFacade;
use App\Utils\ConfigHelper;
use Illuminate\Support\Facades\Cookie;

class Manager
{
    /**
     * @var Device
     */
    protected $device;

    public function setDevice($device)
    {
        $this->device = $device;
        return $this;
    }

    public function getDevice()
    {
        return $this->device;
    }

    public function getId()
    {
        return is_null($this->device) ? null : $this->device->getKey();
    }

    public function fetchFromRequestHeader(Request $request)
    {
        if ($request->ifHeaderJson(ConfigHelper::get('client.headers.device'), $headerValue, true)
            && !empty($headerValue['provider'])
            && !empty($headerValue['secret'])) {
            return $this->setDevice(
                (new DeviceRepository())->notStrict()
                    ->getByProviderAndSecret(
                        $headerValue['provider'],
                        $headerValue['secret']
                    )
            );
        }
        return $this;
    }

    public function fetchFromRequestCookie(Request $request)
    {
        if ($request->ifCookieJson(ClientSettingsFacade::getCookie('device'), $cookieValue, true)
            && !empty($cookieValue['provider'])
            && !empty($cookieValue['secret'])) {
            return $this->setDevice(
                (new DeviceRepository())->notStrict()
                    ->getByProviderAndSecret($cookieValue['provider'], $cookieValue['secret'])
            );
        }
        return $this->setDevice(
            (new DeviceRepository())->save(
                Device::PROVIDER_BROWSER,
                null,
                $request->getClientIps()
            )
        )->storeCookie();
    }

    public function storeCookie()
    {
        Cookie::queue(
            ClientSettingsFacade::getCookie('device'),
            json_encode([
                'provider' => $this->device->provider,
                'secret' => $this->device->secret,
            ]),
            2628000
        ); // 5 years = forever
        return $this;
    }
}
