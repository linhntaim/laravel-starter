<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\HandledFileRepository;
use App\Utils\HandledFiles\Filer\ChunkedFiler;

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

    public function store(Request $request)
    {
        if ($request->has('_chunk_init')) {
            return $this->storeChunkInit($request);
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
                $request->input('chunk_index')
            )
            ->join();

        return $this->responseModel([
            'chunks_id' => $joiner->getChunksId(),
            'joined' => $joiner->joined(),
        ]);
    }
}
