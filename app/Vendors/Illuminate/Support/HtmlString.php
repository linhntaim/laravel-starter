<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Vendors\Illuminate\Support;

use Illuminate\Support\HtmlString as BaseHtmlString;

class HtmlString extends BaseHtmlString
{
    public function escape($flags = ENT_QUOTES)
    {
        if ($this->isNotEmpty()) {
            $this->html = htmlspecialchars($this->html, $flags);
        }
        return $this;
    }

    public function break()
    {
        if ($this->isNotEmpty()) {
            $this->html = nl2br($this->html);
        }
        return $this;
    }

    public static function escapes($html, $break = false, callable $moreCallback = null)
    {
        if (is_string($html)) {
            return tap(new static($html), function (HtmlString $htmlString) use ($break) {
                $htmlString->escape();
                if ($break) {
                    $htmlString->break();
                }
            })->toHtml();
        }
        if (is_iterable($html)) {
            foreach ($html as &$item) {
                $item = static::escapes($item, $break, $moreCallback);
            }
            return $html;
        }
        if (!is_null($moreCallback) && !is_null($more = $moreCallback($html, $break, $moreCallback))) {
            return $more;
        }
        return $html;
    }
}
