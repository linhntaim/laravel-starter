<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

use Jenssegers\Agent\Agent;

class ClientHelper
{
    public static function information()
    {
        $agent = new Agent();
        $info = [];
        if ($agent->isMobile() || $agent->isTablet()) {
            $info[] = $agent->device();
        }
        $info[] = $agent->platform() . ' (' . $agent->version($agent->platform()) . ')';
        $info[] = $agent->browser() . ' (' . $agent->version($agent->browser()) . ')';
        return implode(', ', $info);
    }

    public static function userAgent()
    {
        return request()->header('User-Agent');
    }
}
