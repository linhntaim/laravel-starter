<?php

namespace App\Utils\Theme;

use App\Utils\ConfigHelper;
use App\Vendors\Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

abstract class Theme
{
    public const NAME = 'theme';
    public const DISPLAY_NAME = 'Theme';

    protected $titleComplementUsed = true;

    protected $titleComplement = null;

    protected $titleSeparator = '|';

    protected $titleReverse = true;

    public function instance()
    {
        return $this;
    }

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
        return [];
    }

    /**
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @return View|Factory
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
     * @param array $data
     * @param array $mergeData
     * @param string $view
     * @return View|Factory
     */
    public function pageHome($data = [], $mergeData = [], $view = 'welcome')
    {
        return $this->pageView($view, $data, $mergeData);
    }

    /**
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @return View|Factory
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

    public function trans($key, array $replace = [], $custom = false, $locale = null)
    {
        return $custom ?
            trans(sprintf('themes.custom.%s.%s', $this->getName(), $key), $replace, $locale)
            : trans('themes.' . $key, $replace, $locale);
    }

    public function transChoice($key, $number, array $replace = [], $custom = false, $locale = null)
    {
        return $custom ?
            trans_choice(sprintf('themes.custom.%s.%s', $this->getName(), $key), $number, $replace, $locale)
            : trans_choice('themes.' . $key, $number, $replace, $locale);
    }

    protected function getTitleComplement()
    {
        if (!$this->titleComplementUsed) {
            return null;
        }
        return is_null($this->titleComplement) ? config('app.name') : $this->titleComplement;
    }

    protected function getTitleSeparator()
    {
        return ' ' . $this->titleSeparator . ' ';
    }

    protected function getTitleReverse()
    {
        return $this->titleReverse;
    }

    /**
     * @param string|string[]|array|null $titles
     * @param string|null $complement
     * @return HtmlString
     */
    public function title($titles = null, $complement = null)
    {
        if (is_null($complement)) {
            if (is_null($titles) && ConfigHelper::get('app.id') == 'base') {
                $titles = 'Laravel Starter';
                $complement = 'Nguyen Tuan Linh';
            }
            else {
                $complement = $this->getTitleComplement();
            }
        }
        $titles = (array)$titles;
        array_unshift($titles, $complement);
        $titles = array_map(function ($title) {
            return (new HtmlString($title))->escape()->toHtml();
        }, $titles);
        if ($this->getTitleReverse()) {
            $titles = array_reverse($titles);
        }
        return new HtmlString(implode($this->getTitleSeparator(), $titles));
    }
}
