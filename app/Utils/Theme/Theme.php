<?php

namespace App\Utils\Theme;

abstract class Theme
{
    const NAME = 'theme';
    const DISPLAY_NAME = 'Theme';

    public function getName()
    {
        return static::NAME;
    }

    public function getDisplayName()
    {
        return static::DISPLAY_NAME;
    }

    public function share()
    {
        return [
            'name' => $this->getName(),
            'display_name' => $this->getDisplayName(),
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
        return view(sprintf('themes.%s.pages.%s', $this->getName(), $view), $data, $mergeData);
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
