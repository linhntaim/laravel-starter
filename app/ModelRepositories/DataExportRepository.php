<?php

namespace App\ModelRepositories;

use App\Configuration;
use App\Exports\Export;
use App\Jobs\ExportJob;
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

    public function search($search = [], $paging = Configuration::FETCH_PAGING_YES, $itemsPerPage = Configuration::DEFAULT_ITEMS_PER_PAGE, $sortBy = null, $sortOrder = null)
    {
        $query = $this->query();

        if (!empty($search['names'])) {
            $query->whereIn('name', $search['names']);
        }

        if (!empty($sortBy)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        if ($paging == Configuration::FETCH_PAGING_NO) {
            return $query->get();
        } elseif ($paging == Configuration::FETCH_PAGING_YES) {
            return $query->paginate($itemsPerPage);
        }

        return $query;
    }

    public function createWithAttributesAndExport(array $attributes, Export $export)
    {
        $attributes['name'] = $export->getName();
        $attributes['state'] = DataExport::STATE_EXPORTING;
        $attributes['payload'] = serialize($export);
        $this->createWithAttributes($attributes);
        ExportJob::dispatch($this->model, $export);
        return $this->model;
    }
}
