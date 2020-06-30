<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\ModelRepositories\ModelRepository;

class ApiController extends Controller
{
    use ApiResponseTrait, ItemsPerPageTrait;

    /**
     * @var ModelRepository|mixed
     */
    protected $modelRepository;

    /**
     * @var string
     */
    protected $modelTransformerClass;

    public function __construct()
    {
        $this->withThrottlingMiddleware();
    }

    #region Index
    protected function search(Request $request)
    {
        return [];
    }

    public function index(Request $request)
    {
        $models = $this->modelRepository->search(
            $this->search($request),
            $this->paging(),
            $this->itemsPerPage(),
            $this->sortBy(),
            $this->sortOrder()
        );
        return $this->responseModel($models);
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
        if ($request->has('_delete')) {
            return $this->bulkDestroy($request);
        }

        $this->storeValidated($request);

        $this->transactionStart();
        return $this->responseModel($this->storeExecute($request));
    }

    #endregion

    public function show(Request $request, $id)
    {
        return $this->responseModel($this->modelRepository->model($id));
    }

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
        return $this->responseModel($this->updateExecute($request));
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

    public function bulkDestroy(Request $request)
    {
        $this->bulkDestroyValidated($request);
        $ids = $request->input('ids');
        $this->transactionStart();
        $this->modelRepository->deleteWithIds($ids);
        return $this->responseSuccess();
    }

    public function destroy(Request $request, $id)
    {
        $this->modelRepository->model($id);
        $ids = [$id];
        $this->transactionStart();
        $this->modelRepository->deleteWithIds($ids);
        return $this->responseSuccess();
    }
    #endregion
}
