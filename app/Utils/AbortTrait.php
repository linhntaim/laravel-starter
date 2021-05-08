<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

trait AbortTrait
{
    protected function abort($code, $message = '', $transOptions = [])
    {
        abort(
            $code,
            empty($message) ?
                transIf(
                    'error.def.abort.' . $code,
                    $message,
                    $transOptions['replace'] ?? [],
                    $transOptions['locale'] ?? null
                )
                : $message
        );
    }

    protected function abort400($message = '', $transOptions = [])
    {
        $this->abort(400, $message, $transOptions);
    }

    protected function abort401($message = '', $transOptions = [])
    {
        $this->abort(401, $message, $transOptions);
    }

    protected function abort403($message = '', $transOptions = [])
    {
        $this->abort(403, $message, $transOptions);
    }

    protected function abort404($message = '', $transOptions = [])
    {
        $this->abort(404, $message, $transOptions);
    }

    protected function abort500($message = '', $transOptions = [])
    {
        $this->abort(500, $message, $transOptions);
    }

    protected function abort503($message = '', $transOptions = [])
    {
        $this->abort(503, $message, $transOptions);
    }
}
