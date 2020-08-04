<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\Utils\HandledFiles\Helper;
use Illuminate\Support\Facades\File;

class SystemLogController extends ModelApiController
{
    const ALLOWED_LOG_EXTENSIONS = ['log', 'txt'];

    protected $logPath;

    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $systemLogs = [];
        foreach (File::allFiles($this->logPath) as $logFile) {
            $logRealPath = $logFile->getRealPath();
            if (in_array(File::extension($logRealPath), static::ALLOWED_LOG_EXTENSIONS)) {
                $logRelativePath = trim(str_replace($this->logPath, '', $logRealPath), '\\/');
                $systemLogs[] = [
                    'name' => $logRelativePath,
                    'url' => route('admin.system_log.show', ['id' => str_replace('\\', '/', $logRelativePath)]),
                ];
            }
        }

        return $this->responseModel($systemLogs);
    }

    public function show(Request $request, $id)
    {
        $logRealPath = Helper::concatPath($this->logPath, $id);
        if (Helper::hasBackPath($logRealPath)
            || !File::isFile($logRealPath)
            || !in_array(File::extension($logRealPath), static::ALLOWED_LOG_EXTENSIONS)) {
            return $this->abort404();
        }
        return $this->responseDownload($logRealPath);
    }
}
