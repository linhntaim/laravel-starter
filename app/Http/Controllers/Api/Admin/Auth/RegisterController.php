<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\RegisterController as BaseRegisterController;
use App\Http\Requests\Request;
use App\ModelRepositories\AdminRepository;
use App\ModelResources\AdminAccountResource;

class RegisterController extends BaseRegisterController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new AdminRepository();
        $this->setFixedModelResourceClass(
            AdminAccountResource::class,
            $this->modelRepository->modelClass()
        );
    }

    public function storeSocial(Request $request)
    {
        $this->validated($request, [
            'email' => 'nullable|sometimes|max:255',
            'display_name' => 'nullable|sometimes|max:255',
            'provider' => 'required|max:255',
            'provider_id' => 'required|max:255',
        ]);

        return $this->responseModel(
            $this->modelRepository->createWithAttributesFromSocial([
                'display_name' => $request->input('display_name'),
            ], [
                'email' => $request->input('email'),
            ], [
                'provider' => $request->input('provider'),
                'provider_id' => $request->input('provider_id'),
            ])
        );
    }
}
