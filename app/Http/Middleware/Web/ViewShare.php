<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware\Web;

use App\Utils\Theme\AuthViewShare;
use App\Utils\Theme\ViewShareMiddleware;

class ViewShare extends ViewShareMiddleware
{
    protected $viewShareClasses = [
        AuthViewShare::class,
        // TODO:

        // TODO
    ];
}
