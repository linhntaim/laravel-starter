<?php

namespace App\ModelTraits;

use App\Configuration;
use Illuminate\Database\Eloquent\SoftDeletes;

trait UserTrait
{
    use NotifiableTrait, NotifierTrait, ProtectedTrait, SoftDeletes;

    public function getId()
    {
        return $this->getKey();
    }

    public function getPasswordMinLength()
    {
        return Configuration::PASSWORD_MIN_LENGTH;
    }
}
