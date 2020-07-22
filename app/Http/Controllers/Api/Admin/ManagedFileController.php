<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\ManagedFileRepository;

class ManagedFileController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new ManagedFileRepository();
    }

    public function storeCkEditorSimpleUpload(Request $request)
    {
        $this->validated($request, [
            'upload' => 'required|image',
        ]);

        $metaImage = $this->modelRepository->createWithUploadedImage($request->file('upload'));
        return response()->json([
            'url' => $metaImage->url,
        ]);
    }
}
