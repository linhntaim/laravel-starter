<?php

namespace App\Utils;

trait AbortTrait
{
    protected function abort($code, $message = null)
    {
        return abort($code, empty($message) ? trans('error.def.abort.' . $code) : $message);
    }

    protected function abort401($message = null)
    {
        return $this->abort(401, $message);
    }

    protected function abort403($message = null)
    {
        return $this->abort(403, $message);
    }

    protected function abort404($message = null)
    {
        return $this->abort(404, $message);
    }
}
