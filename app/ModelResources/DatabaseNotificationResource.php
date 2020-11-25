<?php

namespace App\ModelResources;

use App\ModelResources\Base\ModelResource;
use App\ModelResources\Base\ModelTransformTrait;
use App\Models\Admin;
use App\Models\DatabaseNotification;

/**
 * Class DatabaseNotificationResource
 * @package App\ModelResources
 * @mixin DatabaseNotification
 */
class DatabaseNotificationResource extends ModelResource
{
    use ModelTransformTrait;

    public function toCustomArray($request)
    {
        return [
            $this->merge($this->toCurrentArray($request)),
            $this->merge([
                'notifier' => [
                    'name' => $this->notifier->preferredName(),
                ],
            ]),
        ];
    }
}
