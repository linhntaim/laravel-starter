<?php

namespace App\ModelRepositories;

use App\Exceptions\Exception;
use App\Models\Device;
use App\Utils\ClientHelper;
use App\Utils\StringHelper;

class DeviceRepository extends ModelRepository
{
    public function modelClass()
    {
        return Device::class;
    }

    /**
     * @param $provider
     * @param $secret
     * @return Device
     * @throws Exception
     */
    public function getByProviderAndSecret($provider, $secret)
    {
        return $this->catch(function () use ($provider, $secret) {
            return $this->query()
                ->where('provider', $provider)
                ->where('secret', $secret)
                ->first();
        });
    }

    /**
     * @param $provider
     * @param $secret
     * @return boolean
     * @throws Exception
     */
    public function hasProviderAndSecret($provider, $secret)
    {
        return $this->catch(function () use ($provider, $secret) {
            return $this->query()
                    ->where('provider', $provider)
                    ->where('secret', $secret)
                    ->count() > 0;
        });
    }

    protected function trySecretWithProvider($provider, $maxTry = 100)
    {
        $try = 0;
        while (($secret = StringHelper::uuid()) && $this->hasProviderAndSecret($provider, $secret) && ++$try) {
            if ($try == $maxTry) {
                return $this->abort403();
            }
        }
        return $secret;
    }

    /**
     * @param string $provider
     * @param string|null $secret
     * @param array|string|null $clientIps
     * @param int|null $userId
     * @return Device
     * @throws Exception
     */
    public function save($provider = Device::PROVIDER_BROWSER, $secret = null, $clientIps = null, $userId = null)
    {
        if (empty($provider)) {
            $provider = Device::PROVIDER_BROWSER;
        }

        $clientIps = empty($clientIps) ? null : json_encode((array)$clientIps);

        $device = empty($secret) ? null : $this->getByProviderAndSecret($provider, $secret);

        if (empty($device)) {
            $this->trySecretWithProvider($provider);
            return $this->createWithAttributes([
                'user_id' => $userId,
                'provider' => $provider,
                'secret' => empty($secret) ? $this->trySecretWithProvider($provider) : $secret,
                'client_ips' => $clientIps,
                'client_agent' => ClientHelper::userAgent(),
                'meta_array_value' => [
                    'client_info' => ClientHelper::information(),
                ],
            ]);
        }

        $this->model($device);
        return $this->updateWithAttributes([
            'user_id' => $userId,
            'client_ips' => $clientIps,
            'client_agent' => ClientHelper::userAgent(),
            'meta_array_value' => [
                'client_info' => ClientHelper::information(),
            ],
        ]);
    }
}
