<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\ModelRepositories\DeviceRepository;
use App\Utils\CurrentDevice;
use Closure;

class Device
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->headers->has('Device')) {
            $device = json_decode($request->headers->get('Device'));
            if ($device) {
                CurrentDevice::set(
                    (new DeviceRepository())->notStrict()
                        ->getByProviderAndSecret($device->provider, $device->secret)
                );
            }
        }
        return $next($request);
    }
}
