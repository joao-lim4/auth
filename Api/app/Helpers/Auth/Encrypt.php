<?php

namespace App\Helpers\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

interface IEncrypt
{
    public static function encode(string $encode): string;
    public static function decode(string $decode):?string;
}


class Encrypt implements IEncrypt {

    protected static $increment =  "__ascii_pse(@_\]@)";

    public static function encode(string $encode): string
    {
        return Crypt::encrypt($encode) . self::$increment . md5(time());
    }

    public static function decode(string $decode): ?string
    {
        try {
            return Crypt::decrypt(explode("__ascii_pse", $decode)[0]);
        } catch (DecryptException $e) {
            return null;
        }
    }
}
