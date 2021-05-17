<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Models;

use App\Models\Base\Model;
use Illuminate\Support\Str;

/**
 * Class Impersonate
 * @package App\Models
 * @property int $user_id
 * @property int $via_user_id
 * @property string $impersonate_token
 * @property string $auth_token
 * @property Admin $admin
 */
class Impersonate extends Model
{
    protected $table = 'impersonates';

    protected $fillable = [
        'user_id',
        'via_user_id',
        'impersonate_token',
        'auth_token',
    ];

    protected $visible = [
        'user_id',
        'via_user_id',
        'impersonate_token',
    ];

    public function admin()
    {
        return $this
            ->belongsTo(Admin::class, 'via_user_id', 'user_id')
            ->with('user');
    }
}
