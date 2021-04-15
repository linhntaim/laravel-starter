<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

trait AbortTrait
{
    protected function abort($code, $message = null, $transOptions = [])
    {
        abort(
            $code,
            empty($message) && trans()->ha ?
                trans(
                    'error.def.abort.' . $code,
                    isset($transOptions['replace']) ? $transOptions['replace'] : [],
                    isset($transOptions['locale']) ? $transOptions['locale'] : null
                )
                : $message
        );
    }

    protected function abort400($message = null, $transOptions = [])
    {
        $this->abort(400, $message, $transOptions);
    }

    protected function abort401($message = null, $transOptions = [])
    {
        $this->abort(401, $message, $transOptions);
    }

    protected function abort403($message = null, $transOptions = [])
    {
        $this->abort(403, $message, $transOptions);
    }

    protected function abort404($message = null, $transOptions = [])
    {
        $this->abort(404, $message, $transOptions);
    }

    protected function abort500($message = null, $transOptions = [])
    {
        $this->abort(500, $message, $transOptions);
    }

    protected function abort503($message = null, $transOptions = [])
    {
        $this->abort(503, $message, $transOptions);
    }
}
