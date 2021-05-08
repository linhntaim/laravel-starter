<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\CryptoJs;

class AES
{
    public static function encrypt($data, $passPhrase, $salt = null)
    {
        $salt = $salt ?: openssl_random_pseudo_bytes(8);
        [$key, $iv] = static::evpkdf($passPhrase, $salt);
        $ct = openssl_encrypt($data, 'aes-256-cbc', $key, true, $iv);
        return static::encode($ct, $salt);
    }

    public static function decrypt($base64, $passphrase)
    {
        [$ct, $salt] = static::decode($base64);
        [$key, $iv] = static::evpkdf($passphrase, $salt);
        return openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    }

    public static function evpkdf($passPhrase, $salt)
    {
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $passPhrase . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        return [$key, $iv];
    }

    public static function decode($base64)
    {
        $data = base64_decode($base64);
        if (substr($data, 0, 8) !== 'Salted__') {
            throw new \InvalidArgumentException();
        }
        $salt = substr($data, 8, 8);
        $ct = substr($data, 16);
        return [$ct, $salt];
    }

    public static function encode($ct, $salt)
    {
        return base64_encode('Salted__' . $salt . $ct);
    }
}
