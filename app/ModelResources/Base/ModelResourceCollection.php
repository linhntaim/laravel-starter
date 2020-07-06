<?php


namespace App\ModelResources\Base;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

class ModelResourceCollection extends AnonymousResourceCollection implements IModelResource
{
    use ModelResourceTrait;

    public static $wrap = 'models';

    public function toModel($request)
    {
        if ($this->resource instanceof AbstractPaginator) {
            return $this->getWrapped(function () use ($request) {
                return $this->preparePaginatedModel($request);
            });
        }

        return $this->getModel($request);
    }

    protected function preparePaginatedModel($request)
    {
        if ($this->preserveAllQueryParameters) {
            $this->resource->appends($request->query());
        } elseif (!is_null($this->queryParameters)) {
            $this->resource->appends($this->queryParameters);
        }

        return (new ModelPaginatedResourceResponse($this))->toModel($request);
    }
}