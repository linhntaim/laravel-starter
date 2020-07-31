<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\AdminRepository;
use App\ModelResources\AdminAccountResource;
use App\Utils\ConfigHelper;

class RegisterController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new AdminRepository();
    }

    public function store(Request $request)
    {
        if (ConfigHelper::isSocialLoginEnabled()) {
            if ($request->has('_social')) {
                return $this->storeSocial($request);
            }
        }
        return $this->responseFail();
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
            $this->setModelResourceClass(AdminAccountResource::class)->modelTransform(
                $this->modelRepository->createWithAttributesFromSocial([
                    'display_name' => $request->input('display_name'),
                ], [
                    'email' => $request->input('email'),
                ], [
                    'provider' => $request->input('provider'),
                    'provider_id' => $request->input('provider_id'),
                ])
            )
        );
    }
}
