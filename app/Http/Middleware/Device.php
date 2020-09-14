<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\ModelRepositories\DeviceRepository;
use App\Utils\ConfigHelper;
use App\Utils\CurrentDevice;
use Closure;

class Device
{
    public function handle(Request $request, Closure $next)
    {
        $deviceHeader = ConfigHelper::get('headers.device');
        if ($request->headers->has($deviceHeader) && ($device = json_decode($request->headers->get($deviceHeader)))) {
            CurrentDevice::set(
                (new DeviceRepository())->notStrict()
                    ->getByProviderAndSecret($device->provider, $device->secret)
            );
        }
        return $next($request);
    }
}
