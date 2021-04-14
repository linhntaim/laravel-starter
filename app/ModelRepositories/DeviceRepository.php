<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\ModelRepositories\Base\ModelRepository;
use App\Models\Device;
use App\Utils\ClientSettings\Facade;
use App\Vendors\Illuminate\Support\Str;

/**
 * Class DeviceRepository
 * @package App\ModelRepositories
 */
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
     */
    public function getByProviderAndSecret($provider, $secret)
    {
        return $this->first(
            $this->query()
                ->where('provider', $provider)
                ->where('secret', $secret)
        );
    }

    /**
     * @param $provider
     * @param $secret
     * @return boolean
     * @throws
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
        while (($secret = Str::uuid()) && $this->hasProviderAndSecret($provider, $secret) && ++$try) {
            if ($try == $maxTry) {
                $this->abort403();
            }
        }
        return $secret;
    }

    /**
     * @param string $provider
     * @param string|null $secret
     * @param array|string|null $clientIps
     * @return Device
     * @throws
     */
    public function save($provider = Device::PROVIDER_BROWSER, $secret = null, $clientIps = null)
    {
        $provider = empty($provider) ? Device::PROVIDER_BROWSER : $provider;
        $secret = empty($secret) ? null : $secret;
        $clientIps = empty($clientIps) ? null : json_encode((array)$clientIps);

        if (!empty($secret)) {
            $this->notStrict()
                ->pinModel()
                ->getByProviderAndSecret($provider, $secret);
        }

        return $this->doesntHaveModel() ? $this->createWithAttributes([
            'provider' => $provider,
            'secret' => empty($secret) ? $this->trySecretWithProvider($provider) : $secret,
            'client_ips' => $clientIps,
            'client_agent' => Facade::getUserAgent(),
            'meta_array_value' => [
                'client_info' => Facade::getInformation(),
            ],
        ]) : $this->updateWithAttributes([
            'client_ips' => $clientIps,
            'client_agent' => Facade::getUserAgent(),
            'meta_array_value' => [
                'client_info' => Facade::getInformation(),
            ],
        ]);
    }
}
