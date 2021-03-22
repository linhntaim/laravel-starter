<?php

namespace App\Utils\Theme;

use App\Http\Requests\Request;
use Closure;

abstract class ViewShareMiddleware
{
    protected $viewShareClasses = [];

    public function handle(Request $request, Closure $next)
    {
        $this->share($request);
        return $next($request);
    }

    public function share(Request $request)
    {
        foreach ($this->viewShareClasses as $viewShareClass) {
            $this->getViewShare($viewShareClass)->share($request);
        }
    }

    /**
     * @param string $viewShareClass
     * @return ViewShare
     */
    protected function getViewShare($viewShareClass)
    {
        return new $viewShareClass;
    }
}
