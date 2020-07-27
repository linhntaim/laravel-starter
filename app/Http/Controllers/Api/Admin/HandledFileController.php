<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\HandledFileController as BaseHandledFileController;
use App\Http\Requests\Request;
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

        $filer = (new ChunkedFiler())->fromChunksIdCompleted($request->input('chunks_id'));
        if ($request->has('public')) {
            $filer->moveToPublic();
        }

        return $this->responseModel($this->modelRepository->createWithFiler($filer));
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
}
