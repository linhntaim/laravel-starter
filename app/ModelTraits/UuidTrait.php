<?php

namespace App\ModelTraits;

use Ramsey\Uuid\Uuid;

trait UuidTrait
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!empty($model->uuids)) {
                foreach ((array)$model->uuids as $uuidKey) {
                    $model->{$uuidKey} = Uuid::uuid1();
                }
            }
        });
    }
}
