<?php

namespace App\ModelTransformers;

use App\Utils\DateTimeHelper;
use App\Utils\LocalizationHelper;

class AccountTransformer extends ModelTransformer
{
    use ModelTransformTrait;

    public function toArray()
    {
        $user = $this->getModel();

        $localizationHelper = LocalizationHelper::getInstance();
        $dateTimeHelper = DateTimeHelper::getInstance();

        return [
            'id' => $user->id,
            'email' => $user->email,

            'localization' => [
                '_ts' => $localizationHelper->getTimestamp(),

                'locale' => $localizationHelper->getLocale(),
                'country' => $localizationHelper->getCountry(),
                'timezone' => $localizationHelper->getTimezone(),
                'currency' => $localizationHelper->getCurrency(),
                'number_format' => $localizationHelper->getNumberFormat(),
                'first_day_of_week' => $localizationHelper->getFirstDayOfWeek(),
                'long_date_format' => $localizationHelper->getLongDateFormat(),
                'short_date_format' => $localizationHelper->getShortDateFormat(),
                'long_time_format' => $localizationHelper->getLongTimeFormat(),
                'short_time_format' => $localizationHelper->getShortTimeFormat(),

                'time_offset' => $dateTimeHelper->getDateTimeOffset(), // seconds
            ],
        ];
    }
}
