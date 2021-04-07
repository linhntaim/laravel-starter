<?php

namespace App\Exceptions;

class IpLimitException extends Exception
{
    const LEVEL = 1;
    const CODE = 403;
}