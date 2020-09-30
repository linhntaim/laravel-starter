<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\Models\Base\Model;
use App\ModelTraits\ArrayValuedAttributesTrait;

/**
 * Class Device
 * @package App\Models
 * @property int $id
 */
class Device extends Model
{
    use ArrayValuedAttributesTrait;

    const PROVIDER_BROWSER = 'browser';

    protected $table = 'devices';

    protected $fillable = [
        'provider',
        'secret',
        'client_ips',
        'client_agent',
        'meta',
        'meta_array_value',
        'meta_array_overridden_value',
    ];

    protected $visible = [
        'provider',
        'secret',
    ];
}
