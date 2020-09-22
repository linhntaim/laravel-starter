<?php

namespace App\Http\Requests;

use App\Models\Admin;

trait ImpersonateRequestTrait
{
    /**
     * @var Admin
     */
    protected $impersonator = null;

    public function setImpersonator(Admin $impersonator = null)
    {
        $this->impersonator = $impersonator;
        return $this;
    }

    public function hasImpersonator()
    {
        return !empty($this->impersonator);
    }

    public function impersonator()
    {
        return $this->impersonator;
    }
}