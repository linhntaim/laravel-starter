<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\File;

class SystemLogController extends ModelApiController
{
    public function index(Request $request)
    {
        $systemLogs = [];
        $logPath = storage_path('logs');
        foreach (File::allFiles($logPath) as $logFile) {
            $logFileRelativePath = trim(str_replace($logPath, '', $logFile->getRealPath()), '\\/');
            $systemLogs[] = [
                'name' => $logFileRelativePath,
                'url' => url('system-log/' . str_replace('\\', '/', $logFileRelativePath)),
            ];
        }

        return $this->responseModel($systemLogs);
    }
}
