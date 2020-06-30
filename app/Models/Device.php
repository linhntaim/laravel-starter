<?php

namespace App\Models;

use App\ModelTraits\ArrayValuedAttributesTrait;
use Illuminate\Database\Eloquent\Model;

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
}
