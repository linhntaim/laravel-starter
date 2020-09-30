<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

use App\ModelRepositories\AppOptionRepository;
use Illuminate\Database\Eloquent\Collection;

class AppOptionHelper
{
    /**
     * @var AppOptionHelper
     */
    private static $instance;

    /**
     * @return AppOptionHelper
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new AppOptionHelper();
        }
        return static::$instance;
    }

    /**
     * @var Collection
     */
    protected $appOptions;

    private function __construct()
    {
        $this->reload();
    }

    protected function reload()
    {
        $this->appOptions = (new AppOptionRepository())->getAll();
    }

    public function getBy($name, $default = null)
    {
        $appOption = $this->appOptions->firstWhere('key', $name);
        return empty($appOption) ? $default : $appOption->value;
    }
}
