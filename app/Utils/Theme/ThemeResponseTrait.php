<?php

namespace App\Utils\Theme;

trait ThemeResponseTrait
{
    protected $viewBase = '';

    public function setViewBase(string $viewBase)
    {
        $this->viewBase = $viewBase;
    }

    protected function themeView($view = 'index', $data = [], $mergeData = [])
    {
        return ThemeFacade::pageView($this->viewBase ? $this->viewBase . '.' . $view : $view, $data, $mergeData);
    }

    protected function themeHome($data = [], $mergeData = [], $view = 'welcome')
    {
        return ThemeFacade::pageHome($data, $mergeData, $view);
    }
}
