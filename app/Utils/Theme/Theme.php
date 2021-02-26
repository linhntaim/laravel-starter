<?php

namespace App\Utils\Theme;

abstract class Theme
{
    const NAME = 'theme';

    public function getName()
    {
        return static::NAME;
    }
}
