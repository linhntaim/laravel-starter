<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\DeviceRepository;
use App\Models\Device;
use App\ModelTransformers\DeviceTransformer;

class DeviceController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new DeviceRepository();
        $this->modelTransformerClass = DeviceTransformer::class;
    }

    public function currentStore(Request $request)
    {
        $this->validated($request, [
            'provider' => 'nullable|sometimes|string',
            'secret' => 'nullable|sometimes|string|max:255',
        ]);

        $currentUser = $request->user();
        return $this->responseModel($this->modelRepository->save(
            $request->input('provider', Device::PROVIDER_BROWSER),
            $request->input('secret', ''),
            $request->getClientIps(),
            empty($currentUser) ? null : $currentUser->id
        ));
    }
}
