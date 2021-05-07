<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\Exports\Base\Export;
use App\Jobs\ExportJob;
use App\ModelRepositories\Base\ModelRepository;
use App\Models\DataExport;

/**
 * Class DataExportRepository
 * @package App\ModelRepositories
 * @method DataExport model($id = null)
 */
class DataExportRepository extends ModelRepository
{
    public function modelClass()
    {
        return DataExport::class;
    }

    protected function searchOn($query, array $search)
    {
        if (!empty($search['name'])) {
            $query->where('name', $search['name']);
        }
        if (!empty($search['names'])) {
            $query->whereIn('name', $search['names']);
        }
        if (!empty($search['created_by'])) {
            $query->where('created_by', $search['created_by']);
        }
        return parent::searchOn($query, $search);
    }

    public function createWithAttributesAndExport(array $attributes, Export $export)
    {
        $attributes['name'] = $export->getName();
        $attributes['state'] = DataExport::STATE_EXPORTING;
        $this->createWithAttributes($attributes);
        ExportJob::dispatch($this->model, $export);
        return $this->model;
    }
}
