<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware;

class AuthorizedWithAdmin extends AuthorizedWithUser
{
    use AdminMiddlewareTrait;

    protected function doesntHave()
    {
        abort(403, static::__transErrorWithModule('must_be_admin'));
    }
}
