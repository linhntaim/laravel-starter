<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\HandledFileRepository;

/**
 * Class HandledFileController
 * @package App\Http\Controllers\Api
 * @property HandledFileRepository $modelRepository
 */
class HandledFileController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new HandledFileRepository();
    }

    public function show(Request $request, $id)
    {
        if ($request->has('_inline')) {
            return $this->getInlineFile($request, $id);
        }

        return $this->responseFail();
    }

    public function getInlineFile(Request $request, $id)
    {
        return $this->modelRepository->model($id)->responseFile();
    }
}
