<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Rules;

use App\Rules\Base\Rule;
use Illuminate\Support\Facades\Hash;

class CurrentPasswordRule extends Rule
{
    public function __construct()
    {
        parent::__construct();

        $this->name = 'current_password';
    }

    public function passes($attribute, $value)
    {
        $user = request()->user();
        return empty($user) ? false : Hash::check($value, $user->password);
    }
}
