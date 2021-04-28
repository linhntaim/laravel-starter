<?php

namespace App\ModelTraits;

use Illuminate\Database\Eloquent\SoftDeletes;

trait UserTrait
{
    use NotifiableTrait, ProtectedTrait, SoftDeletes;
}
