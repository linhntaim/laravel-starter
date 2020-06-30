<?php

namespace App\ModelTransformers;

use App\Models\ManagedFile;

/**
 * Class DataExportTransformer
 * @package App\ModelTransformers
 * @method ManagedFile getModel()
 */
class ManagedFileTransformer extends ModelTransformer
{
    public function toArray()
    {
        $managedFile = $this->getModel();

        return [
            'id' => $managedFile->id,
            'name' => $managedFile->name,
            'url' => $managedFile->url,
        ];
    }
}
