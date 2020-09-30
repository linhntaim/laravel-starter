<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\DeviceRepository;
use App\Models\Device;

class DeviceController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new DeviceRepository();
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
