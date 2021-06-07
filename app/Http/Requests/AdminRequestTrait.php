<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Requests;

use App\Models\Admin;

trait AdminRequestTrait
{
    /**
     * @var Admin|null
     */
    protected $admin = null;

    protected $adminViaMiddleware = false;

    public function setAdminViaMiddleware(Admin $admin = null)
    {
        $this->adminViaMiddleware = true;
        return $this->setAdmin($admin);
    }

    public function setAdmin(Admin $admin = null)
    {
        $this->admin = $admin;
        return $this;
    }

    public function hasAdmin()
    {
        return !empty($this->admin);
    }

    public function hasAdminViaMiddleware()
    {
        return $this->adminViaMiddleware;
    }

    public function admin()
    {
        return $this->admin;
    }
}