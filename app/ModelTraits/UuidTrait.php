<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

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
