<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\Models\Base\Model;

/**
 * Class Device
 * @package App\Models
 * @property int $id
 * @property string $provider
 * @property string $secret
 */
class Device extends Model
{
    public const PROVIDER_BROWSER = 'browser';

    protected $table = 'devices';

    protected $fillable = [
        'provider',
        'secret',
        'client_ips',
        'client_agent',
        'meta',
    ];

    protected $visible = [
        'provider',
        'secret',
        'client_ips',
        'client_agent',
    ];

    protected $casts = [
        'client_ips' => 'array',
        'meta' => 'array',
    ];
}
