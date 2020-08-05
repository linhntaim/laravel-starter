<?php

namespace App\Vendors\Illuminate\Auth\Passwords;

use Closure;
use Illuminate\Auth\Passwords\PasswordBrokerManager as BasePasswordBrokerManager;
use Illuminate\Support\Str;

class PasswordBrokerManager extends BasePasswordBrokerManager
{
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = $config['connection'] ?? null;

        return new DatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire'],
            $config['throttle'] ?? 0
        );
    }

    protected function resolve($name)
    {
        if ($name == 'admins') {
            $config = $this->getConfig($this->getDefaultDriver());
            return new AdminPasswordBroker(
                $this->createTokenRepository($config),
                $this->app['auth']->createUserProvider($config['provider'] ?? null)
            );
        }
        return parent::resolve($name);
    }

    /**
     * @inheritDoc
     */
    public function sendResetLink(array $credentials)
    {
        return $this->__call(__FUNCTION__, [$credentials]);
    }

    /**
     * @inheritDoc
     */
    public function reset(array $credentials, Closure $callback)
    {
        return $this->__call(__FUNCTION__, [$credentials, $callback]);
    }
}
