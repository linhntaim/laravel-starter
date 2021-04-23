<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin;

use App\Exports\Base\Export;
use App\Exports\RoleIndexModelCsvExport;
use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\Imports\Base\Import;
use App\Imports\RoleCsvImport;
use App\ModelRepositories\RoleRepository;
use App\Models\Role;
use Illuminate\Validation\Rule;

/**
 * Class RoleController
 * @package App\Http\Controllers\Api\Admin
 * @property RoleRepository $modelRepository
 */
class RoleController extends ModelApiController
{
    protected $sortByAllows = [
        'created_at',
        'name',
        'display_name',
    ];

    protected function modelRepositoryClass()
    {
        return RoleRepository::class;
    }

    protected function searchParams(Request $request)
    {
        return [
            'name',
            'display_name',
            'permissions' => function ($input) {
                return (array)$input;
            },
        ];
    }

    protected function indexExecute(Request $request)
    {
        $models = parent::indexExecute($request);
        $this->logActionModelList(Role::class, $request->all());
        return $models;
    }

    protected function indexModelExporterClass(Request $request)
    {
        return RoleIndexModelCsvExport::class;
    }

    protected function exportExecute(Request $request, Export $exporter = null)
    {
        parent::exportExecute($request, $exporter);
        $this->logActionModelExport(Role::class, $request->all());
    }

    protected function modelImporterClass(Request $request)
    {
        return RoleCsvImport::class;
    }

    protected function importExecute(Request $request, Import $importer = null)
    {
        parent::importExecute($request, $importer);
        $this->logActionModelImport(Role::class, $request->all());
    }

    protected function storeValidatedRules(Request $request)
    {
        return [
            'name' => 'required|string|max:255|regex:/^[0-9a-z_]+$/|unique:roles,name',
            'display_name' => 'required|max:255',
            'permissions' => 'required|array|exists:permissions,id',
        ];
    }

    protected function storeExecute(Request $request)
    {
        $model = $this->modelRepository->createWithAttributes(
            [
                'name' => $request->input('name'),
                'display_name' => $request->input('display_name'),
                'description' => $request->input('description'),
            ],
            $request->input('permissions')
        );
        $this->logActionModelCreate(Role::class, $model);
        return $model;
    }

    protected function updateValidatedRules(Request $request)
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[0-9a-z_]+$/',
                Rule::unique('roles', 'name')->ignore($this->modelRepository->getId()),
            ],
            'display_name' => 'required|string|max:255',
            'permissions' => 'required|array|exists:permissions,id',
        ];
    }

    protected function updateExecute(Request $request)
    {
        $oldModel = clone $this->modelRepository->model();
        $model = $this->modelRepository->updateWithAttributes(
            [
                'name' => $request->input('name'),
                'display_name' => $request->input('display_name'),
                'description' => $request->input('description'),
            ],
            $request->input('permissions')
        );
        $this->logActionModelEdit(Role::class, $oldModel, $model);
        return $model;
    }

    protected function bulkDestroyExecute(Request $request, $ids)
    {
        $models = $this->modelRepository->getByIds($ids);
        parent::bulkDestroyExecute($request, $ids);
        $this->logActionModelDelete(Role::class, $models);
    }

    protected function destroyExecute(Request $request)
    {
        $model = $this->modelRepository->model();
        parent::destroyExecute($request);
        $this->logActionModelDelete(Role::class, [$model]);
    }
}
