<?php

namespace App\Utils\Files\Filer;

use App\Exceptions\AppException;
use App\Utils\Files\File;
use Intervention\Image\ImageManagerStatic;
use SplFileInfo;

class ImageFiler extends Filer
{
    /**
     * @var \Intervention\Image\Image
     */
    protected $image;

    /**
     * ImageFiler constructor.
     * @param string|SplFileInfo $file
     * @throws AppException
     */
    public function __construct($file)
    {
        parent::__construct($file);

        $this->prepare();
    }

    public function prepare()
    {
        $this->image = ImageManagerStatic::make($this->getRealPath());
        return $this;
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param boolean $aspectRatio
     * @param boolean $upSize
     * @return ImageFiler
     */
    public function resize($width, $height, $aspectRatio = true, $upSize = false)
    {
        $this->image->resize($width, $height, function ($constraint) use ($aspectRatio, $upSize) {
            if ($aspectRatio) {
                $constraint->aspectRatio();
            }
            if ($upSize) {
                $constraint->upsize();
            }
        });
        return $this;
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param integer|null $x
     * @param integer|null $y
     * @return ImageFiler
     */
    public function crop($width, $height, $x = null, $y = null)
    {
        $this->image->crop($width, $height, $x, $y);
        return $this;
    }

    /**
     * @param float $angle
     * @param string $bgColor
     * @return ImageFiler
     */
    public function rotate($angle, $bgColor = '#ffffff')
    {
        $this->image->rotate($angle, $bgColor);
        return $this;
    }

    public function toWhite()
    {
        $this->image->colorize(-100, -100, -100);
        return $this;
    }

    public function save($quality = null, $path = null)
    {
        $this->image->save($path, $quality);

        $this->file = new File($this->file->getRealPath());
        $this->prepare();
    }

    public function move($toDirectory, $name = null, $isRelative = false, $safe = false)
    {
        parent::move($toDirectory, $name, $isRelative, $safe);
        $this->prepare();
        return $this;
    }

    public function delete()
    {
        $this->image->destroy();
        $this->image = null;

        parent::delete();
    }

    /**
     * @param int $width
     * @param int $height
     * @param string $separator1
     * @param string $separator2
     * @return ImageFiler
     */
    public function createThumbnail($width, $height, $separator1 = '_', $separator2 = 'x')
    {
        $fileName = sprintf('%s%s%s%s%s',
            pathinfo($this->file->getFilename(), PATHINFO_FILENAME),
            $separator1,
            $width,
            $separator2,
            $height
        );
        $thumbnailFiler = $this->duplicate($this->getRealDirectory(), $fileName);
        if ($this->image->getWidth() > $width || $this->image->getHeight() > $height) {
            $thumbnailFiler->resize($width, $height);
            $thumbnailFiler->save();
        }
        return $thumbnailFiler;
    }

    public function getResponse()
    {
        return $this->image->response();
    }
}
