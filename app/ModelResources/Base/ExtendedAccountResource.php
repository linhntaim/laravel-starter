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
    protected function toCustomArray($request)
    {
        return [
            $this->merge(parent::toCustomArray($request)),
            $this->merge([
                'settings' => $this->preferredSettings()->toArray(),
            ]),
        ];
    }
}