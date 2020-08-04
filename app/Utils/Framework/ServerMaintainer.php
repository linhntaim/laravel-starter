<?php

namespace App\Utils\Framework;

class ServerMaintainer extends FrameworkHandler
{
    const NAME = 'down';

    protected $data;

    public function exists()
    {
        return app()->isDownForMaintenance() && parent::exists();
    }

    protected function fromContent($content)
    {
        $this->data = $content;
    }

    public function toArray()
    {
        return $this->data;
    }
}
