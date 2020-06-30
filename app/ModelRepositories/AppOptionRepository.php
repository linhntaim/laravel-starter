<?php

namespace App\ModelRepositories;

use App\Models\AppOption;
use App\Utils\Files\Filer\ImageFiler;

class AppOptionRepository extends ModelRepository
{
    public function modelClass()
    {
        return AppOption::class;
    }

    public function save($key, $value)
    {
        return $this->catch(function () use ($key, $value) {
            return $this->query()->updateOrCreate(['key' => $key], ['value' => $value]);
        });
    }

    public function saveMany($options)
    {
        return $this->catch(function () use ($options) {
            foreach ($options as $option) {
                $this->query()->updateOrCreate(['key' => $option['key']], ['value' => $option['value']]);
            }
            return true;
        });
    }

    public function saveAppLogo($imageFiler)
    {
        $imageFiler = (new ImageFiler($imageFiler))->store();
        $imageThumbnail32Filer = $imageFiler->createThumbnail(32, 32);
        $whiteImageFiler = $imageFiler->duplicate(false)->toWhite();
        $whiteImageThumbnail32Filer = $whiteImageFiler->createThumbnail(32, 32);
        return $this->save('app_logo', [
            'original' => $imageFiler->getUrl(),
            's32' => $imageThumbnail32Filer->getUrl(),
            'white_original' => $whiteImageFiler->getUrl(),
            'white_s32' => $whiteImageThumbnail32Filer->getUrl(),
        ]);
    }
}
