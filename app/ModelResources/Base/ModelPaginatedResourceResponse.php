<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources\Base;

use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Support\Arr;

class ModelPaginatedResourceResponse extends PaginatedResourceResponse
{
    public function toModel($request)
    {
        return $this->wrap(
            $this->resource->resolve($request),
            array_merge_recursive(
                $this->paginationInformation($request),
                $this->resource->with($request),
                $this->resource->additional
            )
        );
    }

    protected function paginationInformation($request)
    {
        return [
            'pagination' => Arr::except($this->meta($this->resource->resource->toArray()), [
                'path',
            ]),
        ];
    }
}