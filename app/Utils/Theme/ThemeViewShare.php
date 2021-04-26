<?php

namespace App\Utils\Theme;

use App\Http\Requests\Request;

class ThemeViewShare extends ViewShare
{
    protected function shared(Request $request)
    {
        $locale = app()->getLocale();
        return array_merge([
            'theme' => ThemeFacade::instance(),
            'lang' => str_replace('_', '-', $locale),
            'locale' => $locale,
        ], ThemeFacade::share());
    }
}
