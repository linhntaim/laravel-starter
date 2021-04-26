<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources\Base;

use Illuminate\Http\Resources\Json\ResourceResponse;

class ModelResourceResponse extends ResourceResponse
{
    public function toModel($request)
    {
        return $this->wrap(
            $this->resource->resolve($request),
            $this->resource->with($request),
            $this->resource->additional
        );
    }
}
