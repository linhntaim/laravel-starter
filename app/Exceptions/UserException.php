<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exceptions;

class UserException extends Exception
{
    public const LEVEL = 1;
    public const CODE = 400;
}
