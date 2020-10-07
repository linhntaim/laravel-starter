<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin;

use App\Exports\Base\Export;
use App\Exports\RoleIndexModelExport;
use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\RoleRepository;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Validation\Rule;

class RoleController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new RoleRepository();
    }

    protected function search(Request $request)
    {
        $search = [];
        $input = $request->input('name');
        if (!empty($input)) {
            $search['name'] = $input;
        }
        $input = $request->input('display_name');
        if (!empty($input)) {
            $search['display_name'] = $input;
        }
        $input = $request->input('permissions', []);
        if (!empty($input)) {
            $search['permissions'] = (array)$input;
        }
        $search['except_protected'] = 1;
        return $search;
    }

    protected function indexExecute(Request $request)
    {
        $models = parent::indexExecute($request);
        $this->logAction(ActivityLog::ACTION_LIST, null, [
            'model' => Role::class,
            'params' => $request->all(),
        ]);
        return $models;
    }

    protected function indexModelExporterClass(Request $request)
    {
        return RoleIndexModelExport::class;
    }

    protected function exportExecute(Request $request, Export $exporter = null)
    {
        parent::exportExecute($request, $exporter);
        $this->logAction(ActivityLog::ACTION_EXPORT, null, [
            'model' => Role::class,
            'params' => $request->all(),
        ]);
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
        $this->logAction(ActivityLog::ACTION_CREATE, null, [
            'model' => Role::class,
            'params' => $request->all(),
        ]);
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
        $model = $this->modelRepository->updateWithAttributes(
            [
                'name' => $request->input('name'),
                'display_name' => $request->input('display_name'),
                'description' => $request->input('description'),
            ],
            $request->input('permissions')
        );
        $this->logAction(ActivityLog::ACTION_EDIT, null, [
            'model' => Role::class,
            'id' => $this->modelRepository->getId(),
            'params' => $request->all(),
        ]);
        return $model;
    }

    protected function bulkDestroyExecute(Request $request, $ids)
    {
        parent::bulkDestroyExecute($request, $ids);
        $this->logAction(ActivityLog::ACTION_DELETE, null, [
            'model' => Role::class,
            'params' => $request->all(),
        ]);
    }

    protected function destroyExecute(Request $request)
    {
        parent::destroyExecute($request);
        $this->logAction(ActivityLog::ACTION_DELETE, null, [
            'model' => Role::class,
            'id' => $this->modelRepository->getId(),
        ]);
    }
}
