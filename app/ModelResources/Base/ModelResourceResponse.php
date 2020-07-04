<?php


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