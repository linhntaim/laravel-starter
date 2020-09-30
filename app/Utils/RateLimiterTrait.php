<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

use Illuminate\Cache\CacheManager;
use Illuminate\Cache\RateLimiter;

trait RateLimiterTrait
{
    /**
     * @var RateLimiter
     */
    protected $limiter;

    /**
     * @return RateLimiter
     */
    protected function getLimiter()
    {
        $cacheManager = $this->getCacheManager();
        $needToRefreshCacheStore = $cacheManager->getDefaultDriver() === 'database'
            && app()->runningInConsole();
        if ($needToRefreshCacheStore) {
            $cacheManager->forgetDriver();
        }
        if ($needToRefreshCacheStore || empty($this->limiter)) {
            $this->limiter = new RateLimiter($cacheManager->store());
        }
        return $this->limiter;
    }

    /**
     * @return CacheManager
     */
    protected function getCacheManager()
    {
        return app()->get('cache');
    }
}
