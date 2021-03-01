<?php

namespace App\Http\Middleware\Web;

use App\Http\Requests\Request;
use App\ModelRepositories\DeviceRepository;
use App\Utils\ConfigHelper;
use App\Utils\CurrentDevice;
use Closure;
use Illuminate\Support\Facades\Cookie;

class Device
{
    public function handle(Request $request, Closure $next)
    {
        $deviceCookieName = ConfigHelper::get('web_cookies.device');
        if ($request->cookies->has($deviceCookieName) && ($device = json_decode($request->cookies->get($deviceCookieName)))) {
            CurrentDevice::set(
                (new DeviceRepository())->notStrict()
                    ->getByProviderAndSecret($device->provider, $device->secret)
            );
        } else {
            $device = (new DeviceRepository())->save(
                \App\Models\Device::PROVIDER_BROWSER,
                null,
                $request->getClientIps()
            );
            CurrentDevice::set($device);

            Cookie::queue($deviceCookieName, json_encode([
                'provider' => $device->provider,
                'secret' => $device->secret,
            ]), 2628000); // 5 years = forever
        }
        return $next($request);
    }
}
