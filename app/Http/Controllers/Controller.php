<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\ModelResources\Base\ModelTransformTrait;
use App\Models\HandledFile;
use App\Utils\AbortTrait;
use App\Utils\ActivityLogTrait;
use App\Utils\ClassTrait;
use App\Utils\Database\Transaction\TransactionTrait;
use App\Utils\ValidationTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use ClassTrait, AbortTrait, TransactionTrait, ModelTransformTrait, ActivityLogTrait, ValidationTrait, PagingTrait;

    /**
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @param callable|null $hook
     * @return bool|Validator
     * @throws
     */
    protected function validated(Request $request, array $rules, array $messages = [], array $customAttributes = [], callable $hook = null)
    {
        return $this->validatedData($request->all(), $rules, $messages, $customAttributes, $hook);
    }

    protected function responseContent($content, $status = 200, array $headers = [])
    {
        $this->transactionComplete();
        return response($content, $status, $headers);
    }

    protected function responseFile($file, array $headers = [])
    {
        $this->transactionComplete();
        if ($file instanceof HandledFile) {
            return $file->responseFile($headers);
        }
        return response()->file($file, $headers);
    }

    protected function responseFileStream($file, array $headers = [])
    {
        $this->transactionComplete();
        if ($file instanceof HandledFile) {
            return $file->responseFile($headers);
        }
        $headers['Content-Type'] = mime_content_type($file);
        return response()->stream(function () use ($file) {
            echo file_get_contents($file);
        }, 200, $headers);
    }

    protected function responseDownload($file, $name = null, array $headers = [])
    {
        $this->transactionComplete();
        if ($file instanceof HandledFile) {
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
