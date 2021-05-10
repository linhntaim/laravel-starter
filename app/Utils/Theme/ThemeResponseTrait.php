<?php

namespace App\Utils\Theme;

trait ThemeResponseTrait
{
    protected $viewBase = '';

    protected function setViewBase(string $viewBase)
    {
        $this->viewBase = $viewBase;
    }

    protected function getViewBase()
    {
        return $this->viewBase;
    }

    protected function themeView($view = 'index', $data = [], $mergeData = [])
    {
        return ThemeFacade::pageView(($viewBase = $this->getViewBase()) ?
            $viewBase . '.' . $view : $view, $data, $mergeData);
    }

    protected function themeHome($data = [], $mergeData = [], $view = 'welcome')
    {
        return ThemeFacade::pageHome($data, $mergeData, $view);
    }
}
