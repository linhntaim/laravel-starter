<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelResources\Base;

use App\Http\Requests\Request;

interface IModelResource
{
    /**
     * @param bool $value
     * @return IModelResource
     */
    public function enableWrapping($value = true);

    /**
     * @param Request $request
     * @return mixed
     */
    public function toModel($request);

    /**
     * @param Request $request
     * @return array
     */
    public function model($request = null);
}