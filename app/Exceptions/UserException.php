<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exceptions;

class UserException extends Exception
{
    const LEVEL = 1;
    const CODE = 400;
}
