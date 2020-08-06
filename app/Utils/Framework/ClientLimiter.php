<?php

namespace App\Utils\Framework;

use App\Http\Requests\Request;
use App\ModelRepositories\AppOptionRepository;
use App\Utils\ClientSettings\DateTimer;
use App\Utils\ConfigHelper;
use Symfony\Component\HttpFoundation\IpUtils;
use App\Utils\AppOptionHelper;

class ClientLimiter extends FrameworkHandler
{
    const NAME = 'limit';

    protected $allowed;
    protected $denied;
    protected $admin;
    protected $timeOut;
    protected $expired;

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

    public function setTimeOut(int $time = 3600)
    {
        $this->timeOut = (clone DateTimer::syncNowObject())->addSeconds($time)->format('Y-m-d H:i');
        return $this;
    }

    public function setExpired(bool $expired = true)
    {
        $this->expired = $expired;
        return $this;
    }

    protected function fromContent($content)
    {
        $now =  (clone DateTimer::syncNowObject())->format('Y-m-d H:i');

        $this->setAllowed(isset($content['allowed']) ? $content['allowed'] : [])
            ->setDenied(isset($content['denied']) ? $content['denied'] : [])
            ->setAdmin(isset($content['admin']) && $content['admin'] ? true : false)
            ->setExpired(isset($content['timeOut']) && $now > $content['timeOut'] ? true : false);
    }

    public function canAccess(Request $request, $excepts = [])
    {
        if ($this->expired) {
            $clientLimit = AppOptionHelper::getInstance()->getBy('client_limit');
            $timeOut = ConfigHelper::get('client_limit.time_out');
            $this->setAllowed($clientLimit['allowed'])
                ->setDenied($clientLimit['denied'])
                ->setAdmin($clientLimit['admin'])
                ->setTimeOut(intval($timeOut) > 0 ? $timeOut : null)
                ->saveFile();
        }

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
            'timeOut' => $this->timeOut
        ];
    }

    public function valueForKeyClientLimit()
    {
        return [
            'allowed' => $this->allowed,
            'denied' => $this->denied,
            'admin' => $this->admin,
        ];
    }

    public function save()
    {
        (new AppOptionRepository())->save('client_limit',$this->valueForKeyClientLimit());

        return $this->saveFile();
    }

    public function saveFile()
    {
        return parent::save();
    }
}
