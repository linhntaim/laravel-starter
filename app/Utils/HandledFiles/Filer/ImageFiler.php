<?php

namespace App\Utils\HandledFiles\Filer;

use App\Exceptions\AppException;
use App\Utils\HandledFiles\Storage\LocalStorage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;

/**
 * Class ImageFiler
 * @package App\Utils\HandledFiles\Filer
 * @method ImageFiler fromUploaded(UploadedFile $uploadedFile, $toDirectory = null, $keepOriginalName = true)
 */
class ImageFiler extends Filer
{
    /**
     * @var Image
     */
    protected $image;

    public function fromExternal($url)
    {
        throw new AppException('Not supported');
    }

    public function fromExisted($file, $toDirectory = null, $keepOriginalName = true)
    {
        parent::fromExisted($file, $toDirectory, $keepOriginalName);
        return $this->imagePrepare();
    }

    public function fromCreating($name = null, $extension = null, $toDirectory = '')
    {
        throw new AppException('Not supported');
    }

    /**
     * @return ImageFiler
     * @throws
     */
    public function imagePrepare()
    {
        if (($storage = $this->getOriginStorage()) && $storage instanceof LocalStorage) {
            try {
                $this->image = ImageManagerStatic::make($storage->getRealPath());
            } catch (\Exception $exception) {
                throw AppException::from($exception);
            }
        }
        return $this;
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param boolean $aspectRatio
     * @param boolean $upSize
     * @return ImageFiler
     */
    public function imageResize($width, $height, $aspectRatio = true, $upSize = false)
    {
        if ($this->image) {
            $this->image->resize($width, $height, function ($constraint) use ($aspectRatio, $upSize) {
                if ($aspectRatio) {
                    $constraint->aspectRatio();
                }
                if ($upSize) {
                    $constraint->upsize();
                }
            });
        }
        return $this;
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param integer|null $x
     * @param integer|null $y
     * @return ImageFiler
     */
    public function imageCrop($width, $height, $x = null, $y = null)
    {
        if ($this->image) {
            $this->image->crop($width, $height, $x, $y);
        }
        return $this;
    }

    /**
     * @param float $angle
     * @param string $bgColor
     * @return ImageFiler
     */
    public function imageRotate($angle, $bgColor = '#ffffff')
    {
        if ($this->image) {
            $this->image->rotate($angle, $bgColor);
        }
        return $this;
    }

    public function imageToWhite()
    {
        if ($this->image) {
            $this->image->colorize(-100, -100, -100);
        }
        return $this;
    }

    public function imageSave($quality = null)
    {
        if ($this->image) {
            $this->image->save(null, $quality);
        }
        return $this;
    }
}
