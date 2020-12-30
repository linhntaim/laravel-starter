<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use Illuminate\Support\Str;

class IndexController extends Controller
{
    public function index(Request $request, $path = null)
    {
        $welcome = function ($htmlIndexFolder = '') {
            $htmlIndexFileNames = ConfigHelper::get('app.html_index.file_names');
            $indexPath = $htmlIndexFolder ? public_path($htmlIndexFolder) . DIRECTORY_SEPARATOR : '';
            foreach ($htmlIndexFileNames as $htmlIndexFileName) {
                if (file_exists($htmlIndexFile = $indexPath . $htmlIndexFileName)) {
                    return $this->responseFile($htmlIndexFile);
                }
            }
            return view('welcome');
        };
        if ($path) {
            $htmlIndexFolderNames = ConfigHelper::get('app.html_index.folder_names');
            foreach ($htmlIndexFolderNames as $htmlIndexFolderName) {
                if (Str::startsWith($path, $htmlIndexFolderName)) {
                    return $welcome($htmlIndexFolderName);
                }
            }
        }
        return $welcome();
    }
}