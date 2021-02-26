<?php

namespace App\Utils\Theme;

trait ThemeResponseTrait
{
    protected function view($view = 'index', $data = [], $mergeData = [])
    {
        return ThemeFacade::view($view, $data, $mergeData);
    }
}
