<?php

namespace App\Utils\Theme;

use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Theme::class, function () {
            $themes = ThemeHelper::themes();
            $routeBasedThemes = ThemeHelper::routeBasedThemes();
            $themeClass = $themes[ThemeHelper::defaultTheme()];
            $request = request();
            if ($request->has(ThemeHelper::REQUEST_PARAM_THEME)) {
                $themeName = $request->input(ThemeHelper::REQUEST_PARAM_THEME);
                if (isset($themes[$themeName])) {
                    $themeClass = $themes[$themeName];
                }
            } elseif (!empty($routeBasedThemes)) {
                foreach ($routeBasedThemes as $routeMatch => $themeName) {
                    if ($request->possiblyIs($routeMatch)) {
                        if (isset($themes[$themeName])) {
                            $themeClass = $themes[$themeName];
                        }
                        break;
                    }
                }
            }
            return new $themeClass();
        });
    }

    public function boot()
    {
    }
}
