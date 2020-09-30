<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Exceptions;

class DatabaseException extends Exception
{
    const LEVEL = 2;
    const CODE = 503;
}
