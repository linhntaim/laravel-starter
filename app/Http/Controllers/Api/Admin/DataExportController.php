<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\DataExportRepository;

/**
 * Class DataExportController
 * @package App\Http\Controllers\Api\Admin
 * @property DataExportRepository $modelRepository
 */
class DataExportController extends ModelApiController
{
    protected function modelRepositoryClass()
    {
        return DataExportRepository::class;
    }

    protected function searchParams(Request $request)
    {
        return [
            'name',
            'created_by',
            'names' => function ($input) {
                return (array)$input;
            },
        ];
    }

    public function show(Request $request, $id)
    {
        if ($request->has('_download')) {
            return $this->download($request, $id);
        }
        return $this->responseFail();
    }

    private function download(Request $request, $id)
    {
        $export = $this->modelRepository->model($id);

        return $this->responseDownload($export->file);
    }
}
