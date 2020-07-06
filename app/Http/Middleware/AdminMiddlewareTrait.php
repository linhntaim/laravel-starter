<?php

namespace App\Http\Middleware;

use App\Http\Requests\Request;
use App\ModelRepositories\AdminRepository;

trait AdminMiddlewareTrait
{
    protected function getAdmin(Request $request)
    {
        return $request->hasAdminViaMiddleware() ?
            $request->admin()
            : $request->setAdminViaMiddleware((new AdminRepository())->notStrict()->getById($this->auth->user()->id))
                ->admin();
    }
}
