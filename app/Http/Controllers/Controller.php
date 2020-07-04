<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
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
    use ClassTrait, AbortTrait, TransactionTrait, ModelTransformTrait, ValidationTrait, ItemsPerPageTrait;

    /**
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return bool
     * @throws
     */
    protected function validated(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        return $this->validatedData($request->all(), $rules, $messages, $customAttributes);
    }

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

    protected function responseNoCache($response)
    {
        $response->headers->set('Cache-Control', 'no-cache, no-store');
        $response->headers->set('Pragma', 'no-cache');
        return $response;
    }
}
