<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Framework;

use App\Http\Requests\Request;
use App\ModelRepositories\AppOptionRepository;
use App\Utils\ClientSettings\DateTimer;
use App\Utils\ConfigHelper;
use App\Utils\AppOptionHelper;
use App\Utils\IpLimiterTrait;

class ClientLimiter extends FrameworkHandler
{
    use IpLimiterTrait;

    public const NAME = 'limit';
    public const APP_OPTION_KEY = 'client_limit';

    /**
     * @var bool
     */
    protected $admin;

    public function setAdmin($admin = true)
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
        if (!is_null($retrieved)) {
            return $this;
        }

        // Get from database then cache to file
        if ($this->fromDatabase()) {
            return $this->saveToFile();
        }

        return null;
    }

    protected function fromDatabase()
    {
        $clientLimit = AppOptionHelper::getInstance()->getBy(static::APP_OPTION_KEY, []);
        return !empty($clientLimit) && $this->fromContent($clientLimit);
    }

    protected function fromContent($content)
    {
        $now = DateTimer::syncNowObject()->getTimestamp();
        if (isset($content['timeout']) && $now > $content['timeout']) {
            return false;
        }

        $this->setAllowed($content['allowed'] ?? [])
            ->setDenied($content['denied'] ?? [])
            ->setAdmin(isset($content['admin']) && $content['admin']);
        return true;
    }

    public function canAccess(Request $request, $excepts = [])
    {
        return $this->passAdmin($request) || $this->passRule($request, $excepts);
    }

    protected function passAdmin(Request $request)
    {
        return $this->admin && !$request->is('api/admin/*');
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
