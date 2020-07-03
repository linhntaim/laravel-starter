<?php

namespace App\Utils\ClientSettings;

use App\Exceptions\AppException;
use App\ModelTraits\IUserSettings;
use App\Utils\ConfigHelper;
use Illuminate\Support\Str;

class Manager
{
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
        $this->set(new Settings());
    }

    public function capture()
    {
        return clone $this->settings;
    }

    public function autoFetch()
    {
        return $this->fetchFromRequestHeaders()
            ->fetchFromCurrentUser();
    }

    public function fetchFromRequestHeaders()
    {
        $requestHeaders = request()->headers;
        if ($requestHeaders->has('Client')
            && ($settings = json_decode($requestHeaders->get('Client'), true)) !== false) {
            return $this->update($settings);
        }
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
        if ($user instanceof IUserSettings) {
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
        if ($user instanceof IUserSettings) {
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
        if (($settings = ConfigHelper::get('clients.' . $clientType)) && !empty($settings)) {
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
