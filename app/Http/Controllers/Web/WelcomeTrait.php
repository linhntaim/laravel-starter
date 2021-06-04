<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Web;

use App\Http\Requests\Request;
use App\Utils\ConfigHelper;
use Illuminate\Support\Str;

trait WelcomeTrait
{
    protected function welcome(Request $request, $path = null)
    {
        $welcome = function ($htmlIndexFolder = '') use ($request, $path) {
            $htmlIndexFileNames = ConfigHelper::get('app.html_index.file_names');
            $indexPath = $htmlIndexFolder ? public_path($htmlIndexFolder) . DIRECTORY_SEPARATOR : '';
            foreach ($htmlIndexFileNames as $htmlIndexFileName) {
                if (file_exists($htmlIndexFile = $indexPath . $htmlIndexFileName)) {
                    return $this->welcomeHtmlView($request, $htmlIndexFile);
                }
            }
            return $this->welcomeDefaultView($request, $path);
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

    protected function welcomeHtmlView(Request $request, $htmlFile)
    {
        return $this->responseFile($htmlFile);
    }

    protected function welcomeDefaultView(Request $request, $path = null)
    {
        if (!is_null($path)) {
            $this->abort404();
        }
        return $this->viewHome();
    }
}
