<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources\Base;

/**
 * Class ExtendedAccountResource
 * @package App\ModelResources
 */
abstract class ExtendedAccountResource extends ExtendedUserResource
{
    public function toArray($request)
    {
        return $this->mergeIn([
            parent::toArray($request),
            [
                'settings' => $this->preferredSettings()->toArray(),
            ],
        ]);
    }
}
