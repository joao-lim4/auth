<?php

namespace App\Http\Middleware;

use Closure;
use Laravel\Lumen\Http\Request;
use App\Helpers\Middleware\TokenIsValid;

class Auth
{

    private function checkBearerToken(array $authorization): bool
    {
        if(count($authorization) === 0) return false;

        return true;
    }

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $tokenAr = $request->headers->all("authorization");

        if($this->checkBearerToken($tokenAr)){
            if(TokenIsValid::isValid(explode("Bearer ", $tokenAr[0])[1])){
                return $next($request);
            }
        }

        return response()->json([
            "error" => "Não autenticado"
        ], 401);

    }

}
