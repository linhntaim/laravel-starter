<?php

namespace App\ModelTransformers;

use App\Models\DataExport;

/**
 * Class DataExportTransformer
 * @package App\ModelTransformers
 * @method DataExport getModel()
 */
class DataExportTransformer extends ModelTransformer
{
    public function toArray()
    {
        $dataExport = $this->getModel();

        return [
            'id' => $dataExport->id,
            'url' => url('api/admin/data-export', [$dataExport->id]) . '?_download=1',
        ];
    }
}
