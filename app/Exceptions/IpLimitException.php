<?php

namespace App\Exceptions;

class IpLimitException extends Exception
{
    public const LEVEL = 1;
    public const CODE = 403;
}
