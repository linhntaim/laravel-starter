<?php

namespace App\Utils\Theme;

abstract class Theme
{
    const NAME = 'theme';
    const DISPLAY_NAME = 'Theme';
    const APP_TYPE = 'home';

    public function getName()
    {
        return static::NAME;
    }

    public function getDisplayName()
    {
        return static::DISPLAY_NAME;
    }

    public function getAppType()
    {
        return static::APP_TYPE;
    }

    public function share()
    {
        return [
            'theme' => $this,
        ];
    }

    /**
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function view($view, $data = [], $mergeData = [])
    {
        return view($this->viewPath($view), $data, $mergeData);
    }

    public function viewPath($view)
    {
        return sprintf('themes.%s.%s', $this->getName(), $view);
    }

    public function viewExists($view)
    {
        return view()->exists($this->viewPath($view));
    }

    /**
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function pageView($view, $data = [], $mergeData = [])
    {
        return view($this->pageViewPath($view), $data, $mergeData);
    }

    public function pageViewPath($view)
    {
        return sprintf('themes.%s.pages.%s', $this->getName(), $view);
    }

    public function pageViewExists($view)
    {
        return view()->exists($this->pageViewPath($view));
    }

    /**
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    public function asset($path, $secure = null)
    {
        return asset(sprintf('themes/%s/%s', $this->getName(), $path), $secure);
    }
}
