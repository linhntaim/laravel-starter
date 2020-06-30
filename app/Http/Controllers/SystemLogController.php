<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use Illuminate\Support\Str;

class SystemLogController extends Controller
{
    public function show(Request $request, $logFileRelativePath)
    {
        if (Str::startsWith('..\\', $logFileRelativePath)
            || Str::contains('\\..\\', $logFileRelativePath)
            || Str::startsWith('../', $logFileRelativePath)
            || Str::contains('/../', $logFileRelativePath)) {
            return $this->abort404();
        }
        $filePath = storage_path('logs/' . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $logFileRelativePath));
        if (Str::endsWith($filePath, '.log') || Str::endsWith($filePath, '.txt')) {
            return response()->download($filePath);
        }
        return $this->abort404();
    }
}
