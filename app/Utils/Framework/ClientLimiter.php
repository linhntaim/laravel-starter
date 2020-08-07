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

    const APP_OPTION_KEY = 'client_limit';

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

    public function remove()
    {
        (new AppOptionRepository())->deleteWithIds([static::APP_OPTION_KEY]);
        return parent::remove();
    }

    public function retrieve()
    {
        $retrieved = parent::retrieve();
        if (!is_null($retrieved)) return $this;

        // Get from database then cache to file
        if ($this->fromDatabase()) {
            return $this->saveToFile();
        }

        return null;
    }

    protected function fromDatabase()
    {
        $clientLimit = AppOptionHelper::getInstance()->getBy(static::APP_OPTION_KEY, []);
        return empty($clientLimit) ? false : $this->fromContent($clientLimit);
    }

    protected function fromContent($content)
    {
        $now = DateTimer::syncNowObject()->getTimestamp();
        if (isset($content['timeout']) && $now > $content['timeout']) {
            return false;
        }

        $this->setAllowed(isset($content['allowed']) ? $content['allowed'] : [])
            ->setDenied(isset($content['denied']) ? $content['denied'] : [])
            ->setAdmin(isset($content['admin']) && $content['admin'] ? true : false);
        return true;
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

    public function save()
    {
        return $this->saveToDatabase()->saveToFile();
    }

    protected function saveToDatabase()
    {
        (new AppOptionRepository())->save(static::APP_OPTION_KEY, $this->toDatabase());
        return $this;
    }

    protected function saveToFile()
    {
        return parent::save();
    }

    protected function toDatabase()
    {
        return [
            'allowed' => $this->allowed,
            'denied' => $this->denied,
            'admin' => $this->admin,
        ];
    }

    public function toArray()
    {
        return array_merge(
            $this->toDatabase(),
            [
                'timeout' => DateTimer::syncNowObject()
                    ->addSeconds(ConfigHelper::get('client_limit_timeout'))
                    ->getTimestamp(),
            ]
        );
    }
}
