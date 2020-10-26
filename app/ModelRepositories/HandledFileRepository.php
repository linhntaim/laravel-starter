<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\ModelRepositories\Base\ModelRepository;
use App\Models\HandledFile;
use App\Models\HandledFileStore;
use App\Utils\ConfigHelper;
use App\Utils\HandledFiles\Filer\Filer;
use App\Utils\HandledFiles\Filer\ImageFiler;
use App\Utils\HandledFiles\Storage\LocalStorage;
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

    /**
     * @param UploadedFile $uploadedFile
     * @param array $options
     * @return HandledFile
     * @throws
     */
    public function createWithUploadedFile(UploadedFile $uploadedFile, $options = [])
    {
        return $this->createWithFiler((new Filer())->fromExisted($uploadedFile, false, false), $options);
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param array $options
     * @param int|null|bool $imageMaxWidth
     * @param int|null|bool $imageMaxHeight
     * @return HandledFile
     * @throws
     */
    public function createWithUploadedImageFile(UploadedFile $uploadedFile, $options = [], $imageMaxWidth = null, $imageMaxHeight = null)
    {
        return $this->createWithImageFiler((new ImageFiler())->fromExisted($uploadedFile, false, false), $options, $imageMaxWidth, $imageMaxHeight);
    }

    /**
     * @param Filer $filer
     * @param array $options
     * @return Filer
     */
    protected function handleFilerWithOptions(Filer $filer, $options = [])
    {
        if (isset($options['inline']) && $options['inline']) {
            $filer->moveToInline();
        } elseif (isset($options['public']) && $options['public']) {
            $filer->moveToPublic();
            if (ConfigHelper::get('handled_file.cloud.enabled')) {
                $filer->moveToCloud(null, true, ConfigHelper::get('handled_file.cloud.only'));
            }
        }
        return $filer;
    }

    /**
     * @param ImageFiler $imageFiler
     * @param array $options
     * @param int|null|bool $imageMaxWidth
     * @param int|null|bool $imageMaxHeight
     * @return HandledFile
     */
    public function createWithImageFiler(ImageFiler $imageFiler, $options = [], $imageMaxWidth = null, $imageMaxHeight = null)
    {
        if ($imageMaxWidth !== false && empty($imageMaxWidth)) {
            $imageMaxWidth = ConfigHelper::get('handled_file.image.max_width');
        }
        if ($imageMaxHeight !== false && empty($imageMaxHeight)) {
            $imageMaxHeight = ConfigHelper::get('handled_file.image.max_height');
        }
        if ($imageMaxWidth || $imageMaxHeight) {
            $imageFiler->imageResize($imageMaxWidth ? $imageMaxWidth : null, $imageMaxHeight ? $imageMaxHeight : null)
                ->imageSave();
        }
        if (ConfigHelper::get('handled_file.image.inline')) {
            $options['inline'] = true;
        }
        return $this->createWithFiler($imageFiler, $options);
    }

    public function createWithFiler(Filer $filer, $options = [])
    {
        $hasPostProcessed = isset($options['has_post_processed']) && $options['has_post_processed'];
        if (!$hasPostProcessed) {
            $filer = $this->handleFilerWithOptions($filer, $options);
        }

        $this->createWithAttributes([
            'title' => (function ($name) {
                $names = explode('.', $name);
                if (count($names) > 1) {
                    array_pop($names);
                    return implode('.', $names);
                }
                return $name;
            })($filer->getName()),
            'name' => $filer->getName(),
            'mime' => $filer->getMime(),
            'size' => $filer->getSize(),
            'options_array_value' => $options,
            'handling' => $hasPostProcessed ? HandledFile::HANDLING_YES : HandledFile::HANDLING_NO,
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

    public function updateWithAttributes(array $attributes = [])
    {
        if (empty($attributes['title'])) {
            unset($attributes['title']);
        }
        if (empty($attributes['name'])) {
            unset($attributes['name']);
        }
        return parent::updateWithAttributes($attributes);
    }

    public function handledWithFiler(Filer $filer)
    {
        if (!$this->model->ready) {
            $filer = $this->handleFilerWithOptions($filer, $this->model->options_array_value);
            $this->updateWithAttributes([
                'handling' => HandledFile::HANDLING_NO,
            ]);
            $this->model->handledFileStores()->delete();
            $filer->eachStorage(function ($name, Storage $storage, $origin) {
                $this->model->handledFileStores()->create([
                    'origin' => $origin ? HandledFileStore::ORIGIN_YES : HandledFileStore::ORIGIN_NO,
                    'store' => $name,
                    'data' => $storage->getData(),
                ]);
            });
        }
        return $this->model;
    }

    public function handlePostProcessed(callable $postProcessedCallback)
    {
        if (($originStorage = $this->model->originStorage) && $originStorage instanceof LocalStorage) {
            $postProcessedCallback($this->model);
        } else {
            if (!$this->model->ready) {
                $this->updateWithAttributes([
                    'handling' => HandledFile::HANDLING_NO,
                ]);
            }
        }
        return $this->model;
    }
}
