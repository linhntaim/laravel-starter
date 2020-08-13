<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\RegisterController as BaseRegisterController;
use App\Http\Requests\Request;
use App\ModelRepositories\UserRepository;
use App\ModelResources\UserAccountResource;

class RegisterController extends BaseRegisterController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new UserRepository();
    }

    public function storeSocial(Request $request)
    {
        $this->validated($request, [
            'email' => 'nullable|sometimes|max:255',
            'provider' => 'required|max:255',
            'provider_id' => 'required|max:255',
        ]);

        return $this->responseModel(
            $this->setModelResourceClass(UserAccountResource::class)->modelTransform(
                $this->modelRepository->createWithAttributesFromSocial([
                    'email' => $request->input('email'),
                ], [
                    'provider' => $request->input('provider'),
                    'provider_id' => $request->input('provider_id'),
                ])
            )
        );
    }
}
