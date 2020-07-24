<?php

namespace App\ModelRepositories;

use App\ModelRepositories\Base\ModelRepository;
use App\Models\HandledFile;
use App\Models\HandledFileStore;
use App\Utils\ConfigHelper;
use App\Utils\HandledFiles\Filer\Filer;
use App\Utils\HandledFiles\Filer\ImageFiler;
use App\Utils\HandledFiles\Storage\Storage;
use Illuminate\Http\UploadedFile;

/**
 * Class HandledFileRepository
 * @package App\ModelRepositories
 * @property HandledFile $model
 */
class HandledFileRepository extends ModelRepository
{
    public function modelClass()
    {
        return HandledFile::class;
    }

    public function createWithUploadedFile(UploadedFile $uploadedFile)
    {
        return $this->createWithFiler((new Filer())->fromExisted($uploadedFile, null, false));
    }

    public function createWithUploadedImage(UploadedFile $uploadedFile)
    {
        $imageFiler = (new ImageFiler())->fromExisted($uploadedFile, null, false);
        $imageMaxWidth = ConfigHelper::get('image.upload.max_width');
        $imageMaxHeight = ConfigHelper::get('image.upload.max_height');
        if ($imageMaxWidth || $imageMaxHeight) {
            $imageFiler->imageResize($imageMaxWidth ? $imageMaxWidth : null, $imageMaxHeight ? $imageMaxHeight : null)
                ->imageSave();
        }
        if (ConfigHelper::get('image.upload.inline')) {
            $imageFiler->moveToInline();
        }
        return $this->createWithFiler($imageFiler);
    }

    public function createWithFiler(Filer $filer)
    {
        if (ConfigHelper::get('managed_file.cloud_enabled')) {
            $filer->moveToCloud(null, true, ConfigHelper::get('managed_file.cloud_only'));
        }

        $this->createWithAttributes([
            'name' => $filer->getName(),
            'mime' => $filer->getMime(),
            'size' => $filer->getSize(),
        ]);

        $filer->eachStorage(function ($name, Storage $storage, $origin) {
            $this->model->handledFileStores()->create([
                'origin' => $origin ? HandledFileStore::ORIGIN_YES : HandledFileStore::ORIGIN_NO,
                'store' => $name,
                'data' => $storage->getData(),
            ]);
        });

        return $this->model;
    }
}
