<?php

namespace App\Utils;

use App\Exceptions\Exception;
use Exception as BaseException;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class LogHelper
{
    protected static $id;

    public static function info($message, $group = 0)
    {
        $request = request();
        if (empty(static::$id)) {
            static::$id = Uuid::uuid1();
        }
        $ip = $request->ip();
        $clientInformation = ClientHelper::information();
        $user = $request->user();

        Log::info(implode(' ', [
            static::$id,
            $group,
            empty($user) ? 0 : $user->id,
            $ip . '[' . $clientInformation . ']',
            $message,
        ]));
    }

    public static function error(BaseException $exception, $group = 0)
    {
        $request = request();
        if (empty(static::$id)) {
            static::$id = Uuid::uuid1();
        }
        $ip = $request->ip();
        $clientInformation = ClientHelper::information();
        $user = $request->user();
        $message = $exception instanceof Exception ?
            json_encode($exception->getMessages()) . ' (Data: ' . json_encode($exception->getAttachedData()) . ')' : $exception->getMessage();
        Log::error(implode(' ', [
            static::$id,
            $group,
            empty($user) ? 0 : $user->id,
            $ip . '[' . $clientInformation . ']',
            $exception->getFile(),
            $exception->getLine(),
            $message,
        ]));
    }
}
