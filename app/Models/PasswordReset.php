<?php

namespace App\Models;

use App\Utils\Facades\ClientSettings;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PasswordReset
 * @package App\Models
 * @property string $sdStExpiredAt
 */
class PasswordReset extends Model
{
    protected $table = 'user_password_resets';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    public function getSdStExpiredAtAttribute()
    {
        $dateTimer = ClientSettings::dateTimer();
        return $dateTimer->compound(
            'shortDate',
            ' ',
            'shortTime',
            $dateTimer->getObject($this->attributes['created_at'])->addMinutes(config('auth.passwords.users.expire'))
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
