<?php

namespace App\ModelRepositories;

use App\Models\AppOption;

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
}
