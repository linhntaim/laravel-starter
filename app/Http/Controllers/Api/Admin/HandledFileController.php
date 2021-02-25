<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\HandledFileController as BaseHandledFileController;
use App\Http\Requests\Request;

class HandledFileController extends BaseHandledFileController
{
    // TODO:
    protected function storeValidated(Request $request)
    {
        parent::storeValidated($request);

        if ($request->has('_event_resource')) {
            $this->validatedData([
                'file' => $request->file('file')->getClientOriginalExtension()
            ], [
                'file' => 'in:' . implode(',', [
                        'ppt', 'doc', 'xls',
                        'pptx', 'docx', 'xlsx',
                        'txt', 'csv', 'pdf',
                        'mp3', 'mp4',
                        'jpg', 'jpeg', 'png', 'gif',
                        'zip', 'tar', 'rar',
                    ]),
            ]);
        }
    }

    // TODO
}
