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

/**
 * Class Manager
 * @package App\Utils\ClientSettings
 * @method string getAppId()
 * @method string getAppKey()
 * @method string getAppName()
 * @method string getAppUrl()
 * @method string getLocale()
 * @method string getCookie($key)
 * @method string getPath($key)
 */
class Manager
{
    /**
     * @var array
     */
    protected $possibleClientIds;

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
        $this->possibleClientIds = array_keys(ConfigHelper::getClient());
        $this->set(new Settings());
    }

    public function capture()
    {
        return clone $this->settings;
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

    /**
     * @param array|Settings $settings
     * @return Manager
     */
    public function update($settings)
    {
        $this->settings->merge($settings);
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

    public function setClient($clientId, $force = false)
    {
        if (($force || $clientId != $this->settings->getAppId()) && in_array($clientId, $this->possibleClientIds)) {
            $settings = ConfigHelper::getClient($clientId);
            $settings['app_id'] = $clientId;
            return $this->update($settings);
        }
        return $this;
    }

    public function setClientFromRequestRoute(Request $request, $force = false)
    {
        $routeBasesClientIds = ConfigHelper::get('client.id_maps.routes', []);
        $appliedClientId = null;
        foreach ($routeBasesClientIds as $routeMatch => $clientId) {
            if ($request->possiblyIs($routeMatch)) {
                $appliedClientId = $clientId;
            }
        }
        if (!is_null($appliedClientId)) {
            return $this->setClient($appliedClientId, $force);
        }
        return $this;
    }

    public function setClientFromRequestHeader(Request $request, $force = false)
    {
        if ($request->ifHeader(ConfigHelper::get('client.headers.client_id'), $headerValue)) {
            return $this->setClient($headerValue, $force);
        }
        return $this;
    }

    public function decryptHeaders(Request $request)
    {
        if ($secret = (function ($secret) {
            $break64 = mb_strlen('base64:');
            return mb_substr($secret, 0, $break64) == 'base64:' ?
                utf8_encode(base64_decode(mb_substr($secret, $break64))) : $secret;
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

    public function fetchFromRequestHeader(Request $request)
    {
        if ($request->ifHeaderJson(ConfigHelper::get('client.headers.settings'), $headerValue)) {
            return $this->update($headerValue);
        }
        return $this;
    }

    public function fetchFromRequestCookie(Request $request)
    {
        if ($request->ifCookieJson($this->settings->getCookie('settings'), $cookieValue)) {
            return $this->update($cookieValue);
        }
        return $this->storeCookie();
    }

    public function storeCookie()
    {
        Cookie::queue(
            $this->settings->getCookie('settings'),
            $this->capture()->toJson(),
            2628000
        ); // 5 years = forever
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
     * @param string $clientId
     * @param callable $callback
     * @return mixed
     */
    public function temporaryFromClient($clientId, callable $callback)
    {
        if (in_array($clientId, $this->possibleClientIds)
            && ($settings = ConfigHelper::getClient($clientId))
            && !empty($settings)) {
            $settings['app_id'] = $clientId;
            return $this->temporary($settings, $callback);
        }
        return $callback();
    }

    /**
     * @param IUser $user
     * @param callable $callback
     * @return mixed
     */
    public function temporaryFromUser($user, callable $callback)
    {
        if ($user instanceof IUser) {
            return $this->temporary($user->preferredSettings(), $callback);
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

    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'get')) {
            return $this->settings->{$name}(...$arguments);
        }

        throw new AppException('Invalid method');
    }
}
