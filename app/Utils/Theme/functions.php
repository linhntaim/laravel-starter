<?php

function themeAsset($path, $secure = null)
{
    return \App\Utils\Theme\ThemeFacade::asset($path, $secure);
}
