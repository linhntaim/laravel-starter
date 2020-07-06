<?php

namespace App\ModelRepositories;

use App\ModelRepositories\Base\ModelRepository;
use App\Models\ManagedFile;
use App\Utils\ConfigHelper;
use App\Utils\Files\Filer\Filer;
use App\Utils\Files\Filer\ImageFiler;
use Illuminate\Http\UploadedFile;

/**
 * Class ManagedFileRepository
 * @package App\ModelRepositories
 * @property ManagedFile $model
 */
class ManagedFileRepository extends ModelRepository
{
    public function modelClass()
    {
        return ManagedFile::class;
    }

    public function createWithUploadedFile(UploadedFile $uploadedFile)
    {
        return $this->createWithFile($uploadedFile->getClientOriginalName(), $uploadedFile);
    }

    public function createWithUploadedImage(UploadedFile $uploadedFile)
    {
        $imageFiler = (new ImageFiler($uploadedFile))->store();
        $imageMaxWidth = ConfigHelper::get('image.upload.max_width');
        $imageMaxHeight = ConfigHelper::get('image.upload.max_height');
        if ($imageMaxWidth || $imageMaxHeight) {
            $imageFiler->resize($imageMaxWidth ? $imageMaxWidth : null, $imageMaxHeight ? $imageMaxHeight : null)
                ->save();
        }
        return ConfigHelper::get('image.upload.inline') ?
            $this->createInlineWithFiler($uploadedFile->getClientOriginalName(), $imageFiler)
            : $this->createWithFiler($uploadedFile->getClientOriginalName(), $imageFiler);
    }

    /**
     * @param string $name
     * @param $file
     * @return ManagedFile
     * @throws
     */
    public function createWithFile(string $name, $file)
    {
        return $this->createWithFiler($name, (new Filer($file))->store());
    }

    /**
     * @param string $name
     * @param Filer $filer
     * @return ManagedFile
     * @throws
     */
    public function createWithFiler(string $name, Filer $filer)
    {
        if (ConfigHelper::get('managed_file.cloud_enabled')) {
            $filer->storeCloud();

            if (ConfigHelper::get('managed_file.cloud_only')) {
                $managedFile = $this->createWithAttributes([
                    'name' => $name,
                    'size' => $filer->getSize(),
                    'type' => $filer->getMimeType(),
                    'cloud_disk' => config('filesystems.cloud'),
                    'cloud_path' => $filer->getCloudPath(),
                    'cloud_url' => $filer->getCloudUrl(),
                ]);
                $filer->delete();
                return $managedFile;
            }

            return $this->createWithAttributes([
                'name' => $name,
                'size' => $filer->getSize(),
                'type' => $filer->getMimeType(),
                'local_disk' => null,
                'local_path' => $filer->getRealPath(),
                'local_url' => $filer->getUrl(),
                'cloud_disk' => config('filesystems.cloud'),
                'cloud_path' => $filer->getCloudPath(),
                'cloud_url' => $filer->getCloudUrl(),
            ]);
        }

        return $this->createWithAttributes([
            'name' => $name,
            'size' => $filer->getSize(),
            'type' => $filer->getMimeType(),
            'local_disk' => null,
            'local_path' => $filer->getRealPath(),
            'local_url' => $filer->getUrl(),
        ]);
    }

    public function createInlineWithFiler(string $name, Filer $filer)
    {
        return $this->createWithAttributes([
            'name' => $name,
            'size' => $filer->getSize(),
            'type' => $filer->getMimeType(),
            'inline' => $filer->getContent(),
        ]);
    }
}
