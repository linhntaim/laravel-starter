<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\Exceptions\AppException;
use App\Imports\Base\Import;
use App\Jobs\ImportJob;
use App\ModelRepositories\Base\ModelRepository;
use App\Models\DataImport;
use App\Utils\ConfigHelper;
use App\Vendors\Illuminate\Support\Facades\App;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\UploadedFile;

/**
 * Class DataImportRepository
 * @package App\ModelRepositories
 * @method DataImport model($id = null)
 */
class DataImportRepository extends ModelRepository
{
    public function modelClass()
    {
        return DataImport::class;
    }

    public function createWithAttributesAndImport(array $attributes, UploadedFile $importFile, Import $import, $jobClass = ImportJob::class)
    {
        $handledFileRepository = new HandledFileRepository();
        if (classImplemented($jobClass, ShouldQueue::class)) {
            if (App::runningInMultipleInstances()) {
                if (ConfigHelper::get('handled_file.cloud.enabled')) {
                    $handledFileRepository->useCloud();
                } else {
                    throw new AppException('Job is being queued when app is running in multiple instances but cloud storage is disabled');
                }
            }
        }
        $attributes['name'] = $import->getName();
        $attributes['file_id'] = $handledFileRepository->createWithUploadedFile($importFile)->id;
        $attributes['state'] = DataImport::STATE_IMPORTING;
        $this->createWithAttributes($attributes);
        $jobClass::dispatch($this->model, $import);
        return $this->model;
    }
}
