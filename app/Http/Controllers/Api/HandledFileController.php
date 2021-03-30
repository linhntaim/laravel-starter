<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\HandledFileRepository;
use App\Utils\HandledFiles\Filer\ChunkedFiler;

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

    protected function storeValidatedRules(Request $request)
    {
        return [
            'file' => 'required|file',
        ];
    }

    protected function storeExecute(Request $request)
    {
        return $this->modelRepository->createWithUploadedFile(
            $request->file('file'),
            [
                'scan' => $request->input('scan') == 1,
                'public' => $request->input('public') == 1,
                'encrypt' => $request->input('encrypt') == 1,
            ],
        );
    }

    public function store(Request $request)
    {
        if ($request->has('_image')) {
            return $this->storeImage($request);
        }
        if ($request->has('_chunk_init')) {
            return $this->storeChunkInit($request);
        }
        if ($request->has('_chunk_complete')) {
            return $this->storeChunkComplete($request);
        }
        if ($request->has('_chunk')) {
            return $this->storeChunk($request);
        }

        return parent::store($request);
    }

    protected function storeImageValidatedRules(Request $request)
    {
        return [
            'file' => 'required|image',
        ];
    }

    protected function storeImageValidated(Request $request)
    {
        $this->validated($request, $this->storeImageValidatedRules($request));
    }

    protected function storeImageExecute(Request $request)
    {
        return $this->modelRepository->createWithUploadedImageFile(
            $request->file('file'),
            [
                'scan' => $request->input('scan') == 1,
                'public' => $request->input('public', 1) == 1,
                'encrypt' => $request->input('encrypt') == 1,
            ],
        );
    }

    private function storeImage(Request $request)
    {
        $this->storeImageValidated($request);

        $this->transactionStart();
        return $this->responseModel(
            $this->storeImageExecute($request)
        );
    }

    private function storeChunkInit(Request $request)
    {
        return $this->responseModel([
            'chunks_id' => ChunkedFiler::generateChunksId(),
        ]);
    }

    private function storeChunkComplete(Request $request)
    {
        $this->validated($request, [
            'chunks_id' => 'required',
        ]);

        return $this->responseModel(
            $this->modelRepository->createWithFiler(
                (new ChunkedFiler())->fromChunksIdCompleted($request->input('chunks_id')),
                [
                    'scan' => $request->input('scan') == 1,
                    'public' => $request->input('public') == 1,
                    'encrypt' => $request->input('encrypt') == 1,
                ],
                $request->input('name')
            )
        );
    }

    private function storeChunk(Request $request)
    {
        $this->validated($request, [
            'chunks_id' => 'required',
            'chunks_total' => 'required',
            'chunk_file' => 'required|file',
            'chunk_index' => 'required',
        ]);

        $joiner = (new ChunkedFiler())
            ->fromChunk(
                $request->input('chunks_id'),
                $request->input('chunks_total'),
                $request->file('chunk_file'),
                intval($request->input('chunk_index'))
            )
            ->join();

        return $this->responseModel([
            'chunks_id' => $joiner->getChunksId(),
            'joined' => $joiner->joined(),
        ]);
    }

    public function storeCkEditorSimpleUpload(Request $request)
    {
        $this->validated($request, [
            'upload' => 'required|image',
        ]);

        $handledFile = $this->modelRepository
            ->createWithUploadedImageFile($request->file('upload'), [
                'scan' => $request->input('scan') == 1,
                'public' => true,
                'encrypt' => $request->input('encrypt') == 1,
            ]);
        return response()->json([
            'url' => $handledFile->url,
        ]);
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
        $handledFile = $this->modelRepository->model($id);
        if (!$handledFile->scanned) {
            $this->abort404();
        }
        if ((!$handledFile->public || $handledFile->encrypted) && !$request->user()) {
            $this->abort404();
        }
        return $handledFile->responseFile();
    }

    protected function updateValidatedRules(Request $request)
    {
        return [
            'title' => 'nullable|sometimes|max:255',
            'name' => 'nullable|sometimes|max:255',
        ];
    }

    protected function updateExecute(Request $request)
    {
        $attributes = [];
        if ($request->has('title')) {
            $attributes['title'] = $request->input('title');
        }
        if ($request->has('name')) {
            $attributes['name'] = $request->input('name');
        }
        return $this->modelRepository->updateWithAttributes($attributes);
    }
}
