<?php

namespace App\Models;

use App\Models\Base\Model;

/**
 * Class UserSocial
 * @package App\Models
 * @property User $user
 */
class UserSocial extends Model
{
    protected $table = 'user_socials';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
    ];

    protected $visible = [
        'provider',
        'provider_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
