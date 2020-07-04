<?php

namespace App\ModelResources\Base;

use Illuminate\Http\Resources\Json\JsonResource;

class ModelResource extends JsonResource
{
    public static $wrap = 'model';
}