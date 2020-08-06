<?php

namespace App\Utils\Framework;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Psy\Util\Json;

abstract class FrameworkHandler implements Arrayable, Jsonable
{
    const NAME = '';

    protected $file;

    public function __construct()
    {
        $this->setFile();
    }

    public function setFile()
    {
        $this->file = storage_path('framework' . DIRECTORY_SEPARATOR . static::NAME);
    }

    public function remove()
    {
        if ($this->exists()) {
            unlink($this->file);
        }
        return $this;
    }

    public function exists()
    {
        return is_file($this->file);
    }

    public function isNotExists()
    {
        return null;
    }

    public function retrieve()
    {
        if ($this->exists()) {
            $content = json_decode(file_get_contents($this->file), true);
            if (!is_null($content)) {
                $this->fromContent($content);
                return $this;
            }
        }

        return $this->isNotExists();
    }

    protected abstract function fromContent($content);

    public function save()
    {
        file_put_contents($this->file, $this->toContent());
        return $this;
    }

    /**
     * @return string
     */
    protected function toContent()
    {
        return $this->toJson();
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
