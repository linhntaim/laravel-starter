<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\Models\Base\Model;
use App\Utils\ClientSettings\DateTimer;
use App\Utils\ClientSettings\Facade;
use Carbon\Carbon;

/**
 * Class PasswordReset
 * @package App\Models
 * @property string $email
 * @property string|null $expiredAt
 * @property string $sdStExpiredAt
 */
class PasswordReset extends Model
{
    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        $this->table = config('auth.passwords.users.table');

        parent::__construct($attributes);
    }

    public function getExpiredAtAttribute()
    {
        return $this->remind('expired_at', function () {
            return ($expire = config('auth.passwords.users.expire')) ?
                Carbon::parse($this->attributes['created_at'])
                    ->addMinutes($expire)
                    ->format(DateTimer::DATABASE_FORMAT)
                : null;
        });
    }

    public function getSdStExpiredAtAttribute()
    {
        return $this->expiredAt ? Facade::dateTimer()->compound(
            'shortDate',
            ' ',
            'shortTime',
            $this->expiredAt
        ) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
