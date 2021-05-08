<?php

namespace App\Utils;

use App\Vendors\Illuminate\Support\Facades\App;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Throwable;

trait ReportExceptionTrait
{
    protected function reportException(Throwable $e)
    {
        App::make(ExceptionHandler::class)->report($e);
    }
}