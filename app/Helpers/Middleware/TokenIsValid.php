<?php

namespace App\Helpers\Middleware;
use App\Helpers\Auth\Encrypt;
use Carbon\Carbon;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

interface ITokenIsValid
{
    public static function isValid(string $token);
}

class TokenIsvalid implements ITokenIsValid {

    private static function parseToken(string $token): ?object
    {
        $decode = Encrypt::decode($token);

        if(is_null($decode)){
            return null;
        }

        return json_decode($decode);
    }

    private static function validateTimeOut(string $timeout):bool
    {
        $dateAt = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
        $timeoutFormat = Carbon::createFromFormat('Y-m-d', $timeout);

        if($timeoutFormat->diffInDays($dateAt) <= 0)
        {
            return false;
        }

        return true;
    }

    private static function deleteToken(string $email, string $token):void
    {
        $usuario = Usuario::where("email", $email)
                            ->where("token_access", $token)
                            ->first();


        if($usuario instanceof Usuario)
        {
            $usuario->update([
                'token_access' => DB::raw(NULL)
            ]);
        }

    }

    private static function validateParseToken(?object $parseToken):bool
    {
        if(is_null($parseToken)) return false;
        if($parseToken->isValid === 0) return false;
        if(is_null($parseToken->email)) return false;

        return true;
    }

    public static function isValid(string $token)
    {
        $parseToken = self::parseToken($token);

        if(self::validateParseToken($parseToken) === false) return false;

        if(self::validateTimeOut($parseToken->timeout) === true) return true;

        self::deleteToken($parseToken->email, $token);

        return false;
    }

}
