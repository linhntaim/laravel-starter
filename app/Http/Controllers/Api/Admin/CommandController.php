<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Artisan;

class CommandController extends ApiController
{
    public function index(Request $request)
    {
        return $this->responseModel(Artisan::all());
    }

    public function run(Request $request)
    {
        Artisan::call($request->input('cmd'), $request->input('params', []));
        return $this->responseSuccess([
            'output' => Artisan::output(),
        ]);
    }
}
