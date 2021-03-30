<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Api\Auth\RegisterController as BaseRegisterController;
use App\Http\Requests\Request;
use App\ModelRepositories\AdminRepository;
use App\ModelResources\AdminAccountResource;

class RegisterController extends BaseRegisterController
{
    protected function getUserRepositoryClass()
    {
        return AdminRepository::class;
    }

    protected function getUserResourceClass()
    {
        return AdminAccountResource::class;
    }

    protected function registerSociallyValidatedRules()
    {
        return array_merge(parent::registerSociallyValidatedRules(), [
            'display_name' => 'nullable|sometimes|max:255',
        ]);
    }

    public function registerSociallyExecuted(Request $request)
    {
        return $this->modelRepository->createWithAttributesFromSocial([
            'display_name' => $request->input('display_name'),
        ], [
            'email' => $request->input('email'),
        ], [
            'provider' => $request->input('provider'),
            'provider_id' => $request->input('provider_id'),
        ]);
    }

    // TODO:

    // TODO
}
