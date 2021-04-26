<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exceptions;

class DatabaseException extends Exception
{
    public const LEVEL = 2;
    public const CODE = 503;
}
