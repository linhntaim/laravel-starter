<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\HandledFileController as BaseHandledFileController;
use App\Http\Requests\Request;
use App\Jobs\VideoJob;
use App\Utils\HandledFiles\Filer\ChunkedFiler;

class HandledFileController extends BaseHandledFileController
{
    public function store(Request $request)
    {
        if ($request->has('_chunk_init')) {
            return $this->storeChunkInit($request);
        }
        if ($request->has('_chunk_complete')) {
            return $this->storeChunkComplete($request);
        }
        if ($request->has('_chunk')) {
            return $this->storeChunk($request);
        }

        return $this->responseFail();
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
                    'public' => $request->input('public') == 1,
                    'has_post_processed' => $request->input('has_post_processed') == 1,
                ]
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

        $handledFile = $this->modelRepository->createWithUploadedImageCasually($request->file('upload'));
        return response()->json([
            'url' => $handledFile->url,
        ]);
    }

    public function update(Request $request, $id)
    {
        if ($request->has('_handle')) {
            return $this->handle($request, $id);
        }
        return parent::update($request, $id);
    }

    public function handle(Request $request, $id)
    {
        $this->modelRepository->model($id);
        return $this->responseModel(
            $this->modelRepository->handlePostProcessed(function ($model) {
                // TODO: Handle something
            })
        );
    }
}
