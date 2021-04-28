<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\WebController;
use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use Illuminate\Support\Str;

class IndexController extends WebController
{
    public function index(Request $request, $path = null)
    {
        $welcome = function ($htmlIndexFolder = '') use ($request, $path) {
            $htmlIndexFileNames = ConfigHelper::get('app.html_index.file_names');
            $indexPath = $htmlIndexFolder ? public_path($htmlIndexFolder) . DIRECTORY_SEPARATOR : '';
            foreach ($htmlIndexFileNames as $htmlIndexFileName) {
                if (file_exists($htmlIndexFile = $indexPath . $htmlIndexFileName)) {
                    return $this->htmlView($request, $htmlIndexFile);
                }
            }
            return $this->defaultView($request, $path);
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

    protected function htmlView(Request $request, $htmlFile)
    {
        return $this->responseFile($htmlFile);
    }

    protected function defaultView(Request $request, $path = null)
    {
        if (!is_null($path)) $this->abort404();
        return $this->viewHome();
    }
}
