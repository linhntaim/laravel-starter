<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\Framework;

class ServerMaintainer extends FrameworkHandler
{
    public const NAME = 'down';

    protected $data;

    public function exists()
    {
        return app()->isDownForMaintenance() && parent::exists();
    }

    protected function fromContent($content)
    {
        $this->data = $content;
        return true;
    }

    public function toArray()
    {
        return $this->data;
    }
}
