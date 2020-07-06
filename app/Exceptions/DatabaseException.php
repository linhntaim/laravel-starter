<?php

namespace App\Exceptions;

class DatabaseException extends Exception
{
    const LEVEL = 2;
    const CODE = 503;
}
