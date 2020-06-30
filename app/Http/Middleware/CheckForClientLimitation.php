<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Requests\Request;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpFoundation\IpUtils;

class CheckForClientLimitation
{
    /**
     * The application implementation.
     *
     * @var Application
     */
    protected $app;

    /**
     * The URIs that should be accessible while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
        '/api/prerequisite',
    ];

    /**
     * Create a new middleware instance.
     *
     * @param Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, Closure $next)
    {
        if (static::hasLimitation()) {
            $data = static::limitation();

            if ($data['admin'] && !$request->is('api/admin/*')) {
                return $next($request);
            }

            if (((empty($data['allowed']) || $this->matchedIps($request->ips(), (array)$data['allowed']))
                && (empty($data['denied']) || !$this->matchedIps($request->ips(), (array)$data['denied'])))) {
                return $next($request);
            }

            if ($this->inExceptArray($request)) {
                return $next($request);
            }

            abort(403, 'Client limitation');
        }

        return $next($request);
    }

    private function matchedIps($matchingIps, $matchedIps)
    {
        foreach ($matchingIps as $matchingIp) {
            if (IpUtils::checkIp($matchingIp, $matchedIps)) return true;
        }
        return false;
    }

    public static function file()
    {
        return storage_path('framework/limit');
    }

    public static function hasLimitation()
    {
        return file_exists(static::file());
    }

    public static function limitation()
    {
        return json_decode(file_get_contents(static::file()), true);
    }

    protected function inExceptArray(Request $request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
