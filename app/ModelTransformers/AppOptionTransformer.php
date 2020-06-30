<?php

namespace App\ModelTransformers;

class AppOptionTransformer extends ModelTransformer
{
    public function toArray()
    {
        $appOption = $this->getModel();

        return [
            'key' => $appOption->key,
            'value' => $appOption->value,
        ];
    }
}
