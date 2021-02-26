<?php

namespace App\Utils\Theme;

trait ThemeResponseTrait
{
    protected $viewBase = '';

    public function setViewBase(string $viewBase)
    {
        $this->viewBase = $viewBase;
    }

    protected function view($view = 'index', $data = [], $mergeData = [])
    {
        return ThemeFacade::view($this->viewBase ? $this->viewBase . '.' . $view : $view, $data, $mergeData);
    }
}
