<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers;

use App\Configuration;
use App\Exceptions\AppException;
use App\Exports\Base\Export;
use App\Exports\Base\IndexModelCsvExport;
use App\Http\Requests\Request;
use App\Imports\Base\Import;
use App\Jobs\ImportJob;
use App\ModelRepositories\Base\ModelRepository;
use App\ModelRepositories\DataExportRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

abstract class ModelApiController extends ApiController
{
    /**
     * @var ModelRepository|mixed
     */
    protected $modelRepository;

    #region Index
    protected function search(Request $request)
    {
        return [];
    }

    public function index(Request $request)
    {
        if ($request->has('_export')) {
            return $this->export($request);
        }
        if ($request->has('_load')) {
            return $this->load($request);
        }

        return $this->responseModel(
            $request->has('_all') ?
                $this->indexAllExecute($request) : $this->indexExecute($request)
        );
    }

    protected function indexExecute(Request $request)
    {
        return $this->sortExecute()->search(
            $this->search($request),
            $this->paging(),
            $this->itemsPerPage()
        );
    }

    protected function indexAllExecute(Request $request)
    {
        return $this->sortExecute()->search(
            $this->search($request),
            Configuration::FETCH_PAGING_NO,
        );
    }

    protected function sortExecute()
    {
        return $this->modelRepository->sort($this->sortBy(), $this->sortOrder());
    }

    protected function load(Request $request)
    {
        return $this->responseModel($this->loadExecute($request), [
            'more' => $this->modelRepository->beenMore(),
        ]);
    }

    protected function loadExecute(Request $request)
    {
        return $this->moreExecute()->search(
            $this->search($request),
            Configuration::FETCH_PAGING_MORE,
            $this->itemsPerPage()
        );
    }

    protected function moreExecute()
    {
        return $this->modelRepository->more($this->moreBy(), $this->moreOrder(), $this->morePivot());
    }
    #endregion

    #region Export
    /**
     * @param Request $request
     * @return string|null
     */
    protected function indexModelExporterClass(Request $request)
    {
        return null;
    }

    /**
     * @param Request $request
     * @return IndexModelCsvExport|null
     */
    protected function indexModelExporter(Request $request)
    {
        $class = $this->indexModelExporterClass($request);
        return $class ?
            new $class($this->search($request), $this->sortBy(), $this->sortOrder()) : null;
    }

    /**
     * @param Request $request
     * @return Export|null
     */
    protected function exporter(Request $request)
    {
        return $this->indexModelExporter($request);
    }

    protected function exportExecute(Request $request, Export $exporter = null)
    {
        if (!$exporter) {
            $exporter = $this->exporter($request);
            if (!$exporter) {
                throw new AppException('Exporter is not implemented');
            }
        }

        $currentUser = $request->user();
        return (new DataExportRepository())->createWithAttributesAndExport(
            [
                'created_by' => $currentUser ? $currentUser->id : null,
            ],
            $exporter
        );
    }

    protected function export(Request $request)
    {
        return $this->responseModel($this->exportExecute($request));
    }

    #endregion

    #region Import
    /**
     * @return string
     */
    protected function modelImporterFileInputKey()
    {
        return 'file';
    }

    /**
     * @param Request $request
     * @return UploadedFile
     */
    protected function modelImporterFile(Request $request)
    {
        return $request->file($this->modelImporterFileInputKey());
    }

    /**
     * @param Request $request
     * @return string|null
     */
    protected function modelImporterClass(Request $request)
    {
        return null;
    }

    /**
     * @param Request $request
     * @return IndexModelCsvExport|null
     */
    protected function modelImporter(Request $request)
    {
        $class = $this->modelImporterClass($request);
        return $class ? new $class($this->modelImporterFile($request)) : null;
    }

    /**
     * @param Request $request
     * @return Export|null
     */
    protected function importer(Request $request)
    {
        return $this->modelImporter($request);
    }

    protected function importExecute(Request $request, Import $importer = null)
    {
        if (!$importer) {
            $importer = $this->importer($request);
            if (!$importer) {
                throw new AppException('Importer is not implemented');
            }
        }

        ImportJob::dispatch($importer);
    }

    protected function importValidatedRules(Request $request)
    {
        return [
            $this->modelImporterFileInputKey() => 'required|file|mimes:csv,txt',
        ];
    }

    protected function importValidated(Request $request)
    {
        $this->validated($request, $this->importValidatedRules($request));
    }

    protected function import(Request $request)
    {
        $this->importValidated($request);
        $this->importExecute($request);
        return $this->responseSuccess();
    }
    #endregion

    #region Store
    protected function storeValidatedRules(Request $request)
    {
        return [];
    }

    protected function storeValidated(Request $request)
    {
        $this->validated($request, $this->storeValidatedRules($request));
    }

    protected function storeExecute(Request $request)
    {
        return null;
    }

    public function store(Request $request)
    {
        if ($request->has('_import')) {
            return $this->import($request);
        }
        if ($request->has('_delete')) {
            return $this->bulkDestroy($request);
        }

        $this->storeValidated($request);

        $this->transactionStart();
        return $this->storeResponse($this->storeExecute($request));
    }

    protected function storeResponse($model)
    {
        return $this->responseModel($model);
    }

    #endregion

    #region Show
    public function showExecute(Request $request, $id)
    {
        return $this->modelRepository->model($id);
    }

    public function show(Request $request, $id)
    {
        return $this->showResponse(
            $this->showExecute($request, $id)
        );
    }

    protected function showResponse($model)
    {
        return $this->responseModel($model);
    }
    #endregion

    #region Update
    protected function updateValidatedRules(Request $request)
    {
        return [];
    }

    protected function updateValidated(Request $request)
    {
        $this->validated($request, $this->updateValidatedRules($request));
    }

    protected function updateExecute(Request $request)
    {
        return null;
    }

    public function update(Request $request, $id)
    {
        if ($request->has('_delete')) {
            return $this->destroy($request, $id);
        }

        $this->modelRepository->model($id);

        $this->updateValidated($request);

        $this->transactionStart();
        return $this->updateResponse($this->updateExecute($request));
    }

    protected function updateResponse($model)
    {
        return $this->responseModel($model);
    }
    #endregion

    #region Destroy
    protected function bulkDestroyValidatedRules(Request $request)
    {
        return [
            'ids' => 'required|array',
        ];
    }

    protected function bulkDestroyValidated(Request $request)
    {
        $this->validated($request, $this->bulkDestroyValidatedRules($request));
    }

    protected function bulkDestroyExecute(Request $request, $ids)
    {
        $this->modelRepository->deleteWithIds($ids);
    }

    public function bulkDestroy(Request $request)
    {
        $this->bulkDestroyValidated($request);
        $this->transactionStart();
        $this->bulkDestroyExecute($request, $request->input('ids'));
        return $this->bulkDestroyResponse();
    }

    protected function bulkDestroyResponse()
    {
        return $this->responseSuccess();
    }

    protected function destroyExecute(Request $request)
    {
        $this->modelRepository->delete();
    }

    public function destroy(Request $request, $id)
    {
        $this->modelRepository->model($id);
        $this->transactionStart();
        $this->destroyExecute($request);
        return $this->destroyResponse();
    }

    protected function destroyResponse()
    {
        return $this->responseSuccess();
    }
    #endregion

    /**
     * @param Model|Collection|LengthAwarePaginator|array $model
     * @return array
     */
    protected function getRespondedModel($model)
    {
        if ($model instanceof Model || $model instanceof Collection || $model instanceof LengthAwarePaginator) {
            $model = $this->modelTransform($model, null, true);
        }
        return is_null($model) ?
            ['model' => null, 'models' => []] :
            (isset($model['model']) || isset($model['models']) ?
                $model : $this->getRespondedDataWithKey($model, Arr::isAssoc($model) ? 'model' : 'models'));
    }

    /**
     * @param Model|Collection|LengthAwarePaginator|array $model
     * @param array $extra
     * @param array $headers
     * @param int $statusCode
     * @param string|null $message
     * @return JsonResponse
     */
    protected function responseModel($model, $extra = [], $headers = [], $statusCode = Response::HTTP_OK, $message = null)
    {
        return $this->responseSuccess(array_merge($this->getRespondedModel($model), $extra), $message, $headers, $statusCode);
    }
}
