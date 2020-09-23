<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\RoleIndexModelExport;
use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\RoleRepository;
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

    protected function indexModelExporterClass(Request $request)
    {
        return RoleIndexModelExport::class;
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
        return $this->modelRepository->createWithAttributes(
            [
                'name' => $request->input('name'),
                'display_name' => $request->input('display_name'),
                'description' => $request->input('description'),
            ],
            $request->input('permissions')
        );
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
        $this->modelRepository->model();
        return $this->modelRepository->updateWithAttributes(
            [
                'name' => $request->input('name'),
                'display_name' => $request->input('display_name'),
                'description' => $request->input('description'),
            ],
            $request->input('permissions')
        );
    }
}
