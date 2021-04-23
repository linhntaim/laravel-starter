<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources;

use App\ModelResources\Base\ModelResource;
use App\ModelResources\Base\ModelTransformTrait;
use App\Models\DatabaseNotification;

/**
 * Class DatabaseNotificationResource
 * @package App\ModelResources
 * @mixin DatabaseNotification
 */
class DatabaseNotificationResource extends ModelResource
{
    use ModelTransformTrait;

    public function toArray($request)
    {
        return $this->mergeInWithCurrentArray($request, [
            [
                // TODO:
                'notifier' => [
                    'name' => $this->notifier->preferredName(),
                ],

                // TODO
            ],
        ]);
    }
}
