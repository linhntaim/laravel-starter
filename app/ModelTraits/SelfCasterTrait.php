<?php

namespace App\ModelTraits;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

trait SelfCasterTrait
{
    /**
     * @var CastsAttributes|string
     */
    protected $casters = [];

    public function setCaster(string $key, $caster)
    {
        $this->casters[$key] = $caster;
        return $this;
    }

    public function getCaster(string $key)
    {
        return isset($this->casters[$key]) ? $this->casters[$key] : null;
    }
}