<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\DeviceRepository;
use App\ModelResources\DeviceResource;
use App\Models\Device;

/**
 * Class DeviceController
 * @package App\Http\Controllers\Api
 * @property DeviceRepository $modelRepository
 */
class DeviceController extends ModelApiController
{
    protected function modelRepositoryClass()
    {
        return DeviceRepository::class;
    }

    protected function modelResourceClass()
    {
        return DeviceResource::class;
    }

    public function currentStore(Request $request)
    {
        $this->validated($request, [
            'provider' => 'nullable|sometimes|string|max:255|regex:/^[a-zA-Z0-9-_]+$/',
            'secret' => 'nullable|sometimes|string|max:255|regex:/^[a-zA-Z0-9-]+$/',
        ]);

        return $this->responseModel(
            $this->modelRepository->save(
                $request->input('provider', Device::PROVIDER_BROWSER),
                $request->input('secret', ''),
                $request->getClientIps()
            )
        );
    }
}
