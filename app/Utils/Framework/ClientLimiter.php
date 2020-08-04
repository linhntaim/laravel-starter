<?php

namespace App\Utils\Framework;

use App\Http\Requests\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class ClientLimiter extends FrameworkHandler
{
    const NAME = 'limit';

    protected $allowed;
    protected $denied;
    protected $admin;

    public function setAllowed(array $allowed = [])
    {
        $this->allowed = $allowed;
        return $this;
    }

    public function setDenied(array $denied = [])
    {
        $this->denied = $denied;
        return $this;
    }

    public function setAdmin(bool $admin = true)
    {
        $this->admin = $admin;
        return $this;
    }

    protected function fromContent($content)
    {
        $this->setAllowed(isset($content['allowed']) ? $content['allowed'] : [])
            ->setDenied(isset($content['denied']) ? $content['denied'] : [])
            ->setAdmin(isset($content['admin']) && $content['admin'] ? true : false);
    }

    public function canAccess(Request $request, $excepts = [])
    {
        return ($this->admin && !$request->is('api/admin/*'))
            || ((empty($this->allowed) || $this->matchedIps($request->ips(), $this->allowed))
                && (empty($this->denied) || !$this->matchedIps($request->ips(), $this->denied)))
            || $this->except($request, $excepts);
    }

    protected function matchedIps($matchingIps, $matchedIps)
    {
        foreach ($matchingIps as $matchingIp) {
            if (IpUtils::checkIp($matchingIp, $matchedIps)) return true;
        }
        return false;
    }

    protected function except(Request $request, $excepts)
    {
        foreach ($excepts as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except) || $request->routeIs($except) || $request->fullUrlIs($except)) {
                return true;
            }
        }

        return false;
    }

    public function toArray()
    {
        return [
            'allowed' => $this->allowed,
            'denied' => $this->denied,
            'admin' => $this->admin,
        ];
    }
}
