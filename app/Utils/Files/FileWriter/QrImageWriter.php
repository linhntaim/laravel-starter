<?php

namespace App\Utils\Files\FileWriter;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * Class QrImageWriter
 * @package App\Utils\Files\FileWriter
 * @property Writer $handler
 */
class QrImageWriter extends FileWriter
{
    const ALLOWED_IMAGE_FORMATS = ['png', 'gif', 'jpg', 'jpeg'];
    const DEFAULT_IMAGE_FORMAT = 'png';
    const DEFAULT_SIZE = 200; // pixels
    const DEFAULT_QUALITY = 100;

    protected $imageFormat;

    public function __construct($name, $stored = false, $toDirectory = '', $isRelative = false)
    {
        $this->imageFormat = is_array($name) ?
            (isset($name['extension']) ? $name['extension'] : static::DEFAULT_IMAGE_FORMAT)
            : (empty($name) ? static::DEFAULT_IMAGE_FORMAT : pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($this->imageFormat, static::ALLOWED_IMAGE_FORMATS)) {
            $this->imageFormat = static::DEFAULT_IMAGE_FORMAT;
        }
        if (is_array($name)) {
            $name['extension'] = $this->imageFormat;
        } else {
            $name = [
                'name' => empty($name) ? null : pathinfo($name, PATHINFO_FILENAME),
                'extension' => $this->imageFormat,
            ];
        }

        parent::__construct($name, $stored, $toDirectory, $isRelative);
    }

    public function openToWrite()
    {
        if (empty($this->handler)) {
            $this->handler = new Writer(new ImageRenderer(
                new RendererStyle(static::DEFAULT_SIZE),
                new ImagickImageBackEnd($this->imageFormat, static::DEFAULT_QUALITY)
            ));
        }

        return $this;
    }

    public function openToAppend()
    {
        return $this->openToWrite();
    }

    public function write($anything)
    {
        if (!empty($this->handler)) {
            $this->handler->writeFile($anything, $this->getRealPath());
        }

        return $this;
    }

    public function close()
    {
        return $this;
    }
}
