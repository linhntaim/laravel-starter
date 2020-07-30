<?php

namespace App\Models;

use App\Models\Base\Model;

class UserSocial extends Model
{
    protected $table = 'user_socials';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
    ];
}
