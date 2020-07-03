<?php

namespace App\ModelTraits;

use App\Models\User;
use App\Utils\LocalizationHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait UserExtendedTrait
 * @package App\ModelTraits
 * @property User $user
 */
abstract class ExtendedUserModel extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function preferredEmail()
    {
        return $this->user->email;
    }

    public function preferredLocalization()
    {
        return $this->user->preferredLocalization();
    }

    public function preferredLocale()
    {
        return $this->user->preferredLocale();
    }
}
