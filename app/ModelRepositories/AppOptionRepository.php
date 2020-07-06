<?php

namespace App\ModelRepositories;

use App\ModelRepositories\Base\ModelRepository;
use App\Models\AppOption;

class AppOptionRepository extends ModelRepository
{
    public function modelClass()
    {
        return AppOption::class;
    }

    public function save($key, $value)
    {
        return $this->updateOrCreateWithAttributes(['key' => $key], ['value' => $value]);
    }

    public function saveMany($options)
    {
        foreach ($options as $option) {
            $this->save($option['key'], $option['value']);
        }
        return true;
    }
}
