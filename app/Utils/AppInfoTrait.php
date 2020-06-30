<?php

namespace App\Utils;


trait AppInfoTrait {

    public  function getAppInfo()
    {
        $appOptions = AppOptionHelper::getInstance();
        return [
            'company_name' => $appOptions->getBy('company_name'),
            'company_short_name' => $appOptions->getBy('company_short_name'),
            'email' => $appOptions->getBy('email'),
            'phone' => $appOptions->getBy('phone'),
            'phone_description' => $appOptions->getBy('phone_description'),
            'url' => $appOptions->getBy('url'),
        ];
    }
}
