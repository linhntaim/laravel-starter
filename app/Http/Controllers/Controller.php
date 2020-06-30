<?php

namespace App\Http\Controllers;

use App\Models\ManagedFile;
use App\ModelTransformers\ModelTransformTrait;
use App\Utils\AbortTrait;
use App\Utils\ClassTrait;
use App\Utils\TransactionTrait;
use App\Utils\ValidationTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use ClassTrait, AbortTrait, TransactionTrait, ModelTransformTrait, ValidationTrait;

    protected function responseFile($file, array $headers = [])
    {
        if ($file instanceof ManagedFile) {
            return $file->responseFile($headers);
        }
        return response()->file($file, $headers);
    }

    protected function responseDownload($file, $name = null, array $headers = [])
    {
        if ($file instanceof ManagedFile) {
            return $file->responseDownload($name, $headers);
        }
        return response()->download($file, $name, $headers);
    }

    protected function noCache($response)
    {
        $response->headers->set('Cache-Control', 'no-cache, max-age=0, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        return $response;
    }
}
