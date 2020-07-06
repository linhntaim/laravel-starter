<?php

namespace App\Http\Controllers;

use App\Utils\Files\FileHelper;
use App\Utils\Files\Filer\ImageFiler;

class FileController extends Controller
{
    protected $filesPath;

    public function __construct()
    {
        $this->filesPath = config('files.path') . DIRECTORY_SEPARATOR;
    }

    protected function filePath($fileRelativePath)
    {
        return $this->filesPath . $fileRelativePath;
    }

    public function show($path = null)
    {
        if (!empty($path) && !FileHelper::getInstance()->hasBackPath($path)) {
            $filePath = $this->filePath($path);
            if (is_file($filePath)) {
                return $this->responseFile($filePath);
            }

            $parts = explode('.', $path);
            $sizeIndex = count($parts) - 2;
            if (isset($parts[$sizeIndex]) && preg_match('/^s(\d+)x(\d+)$/', $parts[$sizeIndex], $matches)) {
                array_splice($parts, $sizeIndex, 1);
                $filePath = $this->filePath(implode('.', $parts));
                if (is_file($filePath)) {
                    $imageType = exif_imagetype($filePath);
                    if ($imageType == IMAGETYPE_JPEG || $imageType == IMAGETYPE_GIF || $imageType == IMAGETYPE_PNG) {
                        return (new ImageFiler($filePath))
                            ->createThumbnail($matches[1], $matches[2], '.s')
                            ->getResponse();
                    }
                }
            }
        }

        return $this->responseFile($this->filePath('sites/avatar.jpg'));
    }
}
