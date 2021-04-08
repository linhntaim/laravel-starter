<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\ClientSettings;

use App\Exceptions\AppException;
use App\Http\Requests\Request;
use App\Models\Base\IUser;
use App\Utils\ConfigHelper;
use App\Utils\CryptoJs\AES;
use App\Utils\LogHelper;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class Manager
{
    protected $possibleClientApps = [];

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var DateTimer
     */
    protected $dateTimer;

    /**
     * @var NumberFormatter
     */
    protected $numberFormatter;

    public function __construct()
    {
        $this->possibleClientApps = array_keys(ConfigHelper::getClientApp());
        $this->set(new Settings());
    }

    public function capture()
    {
        return clone $this->settings;
    }

    public function setClientApp($clientAppId, $force = false)
    {
        if (($force || $clientAppId != $this->settings->getAppId()) && in_array($clientAppId, $this->possibleClientApps)) {
            $clientSettings = ConfigHelper::getClientApp($clientAppId);
            $clientSettings['app_id'] = $clientAppId;
            return $this->update($clientSettings);
        }
        return $this;
    }

    public function setClientAppFromRequestRoute(Request $request)
    {
        $routeBasesClientAppIds = ConfigHelper::get('client.app_id_maps.routes', []);
        $appliedClientAppId = null;
        foreach ($routeBasesClientAppIds as $routeMatch => $clientAppId) {
            if ($request->possiblyIs($routeMatch)) {
                $appliedClientAppId = $clientAppId;
            }
        }
        if (!is_null($appliedClientAppId)) {
            return $this->setClientApp($appliedClientAppId, true);
        }
        return $this;
    }

    public function setClientAppFromRequestHeader(Request $request)
    {
        if ($request->ifHeader(ConfigHelper::get('client.header_client_id'), $headerValue)) {
            return $this->setClientApp($headerValue, true);
        }
        return $this;
    }

    public function decryptHeaders(Request $request)
    {
        if ($secret = (function ($secret) {
            $break64 = mb_strlen('base64:');
            if (mb_substr($secret, 0, $break64) == 'base64:') {
                return utf8_encode(base64_decode(mb_substr($secret, $break64)));
            }
            return $secret;
        })($this->settings->getAppKey())) {
            $headers = ConfigHelper::get('client.headers');
            $headerEncryptExcepts = ConfigHelper::get('client.header_encrypt_excepts');
            foreach ($headers as $header) {
                if (!in_array($header, $headerEncryptExcepts)
                    && $request->ifHeader($header, $headerValue)) {
                    if ($headerValue = AES::decrypt(base64_decode($headerValue), $secret)) {
                        $request->headers->set($header, $headerValue);
                    } else {
                        LogHelper::error(new AppException(sprintf('Header [%s] cannot be decrypted.', $header)));
                    }
                }
            }
        }
        return $this;
    }

    public function fetchFromRequestHeaders(Request $request)
    {
        if ($request->ifHeader(ConfigHelper::get('headers.settings'), $headerValue)) {
            if (is_array($settings = json_decode($headerValue, true))) {
                return $this->update($settings);
            }
        }
        return $this;
    }

    public function fetchFromRequestCookie(Request $request)
    {
        $requestCookies = $request->cookies;
        $settingsCookieName = ConfigHelper::get('web_cookies.settings');
        if (!empty($settingsCookieName) && $requestCookies->has($settingsCookieName)
            && ($settings = json_decode($requestCookies->get($settingsCookieName), true)) !== false) {
            return $this->update($settings);
        }
        return $this->storeCookie();
    }

    public function storeCookie()
    {
        Cookie::queue(ConfigHelper::get('web_cookies.settings'), $this->capture()->toJson(), 2628000); // 5 years = forever
        return $this;
    }

    public function fetchFromCurrentUser()
    {
        if (($user = request()->user())) {
            return $this->fetchFromUser($user);
        }
        return $this;
    }

    public function fetchFromUser($user)
    {
        if ($user instanceof IUser) {
            return $this->update($user->preferredSettings());
        }
        return $this;
    }

    /**
     * @param mixed $user
     * @param callable $callback
     * @return $this|mixed
     */
    public function temporaryFromUser($user, callable $callback)
    {
        if ($user instanceof IUser) {
            return $this->temporary($user->preferredSettings(), $callback);
        }
        return $this;
    }

    /**
     * @param string $clientType
     * @param callable $callback
     * @return mixed
     */
    public function temporaryFromClientType($clientType, callable $callback)
    {
        if (($settings = ConfigHelper::getClientApp($clientType)) && !empty($settings)) {
            return $this->temporary($settings, $callback);
        }
        return $callback();
    }

    /**
     * @param array|Settings $settings
     * @param callable $callback
     * @return mixed
     */
    public function temporary($settings, callable $callback)
    {
        $original = $this->capture();
        try {
            $this->update($settings);

            return $callback();
        } finally {
            $this->set($original);
        }
    }

    /**
     * @param array|Settings $settings
     * @return Manager
     */
    public function update($settings)
    {
        $this->settings->merge($settings);
        return $this->apply();
    }

    /**
     * @param array|Settings $settings
     * @return Manager
     */
    public function set($settings)
    {
        $this->settings = $settings;
        return $this->apply();
    }

    public function apply()
    {
        ConfigHelper::setCurrentLocale($this->settings->getLocale());
        $this->dateTimer = new DateTimer($this->settings);
        $this->numberFormatter = new NumberFormatter($this->settings);
        return $this;
    }

    public function dateTimer()
    {
        return $this->dateTimer;
    }

    public function numberFormatter()
    {
        return $this->numberFormatter;
    }

    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'get')) {
            return $this->settings->{$name}();
        }

        throw new AppException('Invalid method');
    }
}
