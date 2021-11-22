<?php

namespace App\Helpers\Auth;
use App\Helpers\Auth\Encrypt;
use Symfony\Component\HttpFoundation\HeaderBag;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Request\ResponseMessage;

interface IMainAtuh
{
    public function generateToken(?InfosToken $infos):string;
    public function Login(string $email, string $password);
}

class MainAuth implements IMainAtuh {

    protected $InfosTokenInstance;

    public function __construct(string $email="")
    {
        if(strlen($email)){
            $this->InfosTokenInstance = $this->generateInfos($email);
        }
    }


    private function UpdateToken (Usuario $usuarioInstance, $token): void
    {
        DB::transaction(function() use($usuarioInstance, $token){
            $usuarioInstance->update([
                "token_access" => $token,
            ]);
        });
    }


    private function generateInfos(string $email): InfosToken
    {
        return new InfosToken($email);
    }

    private function getToken(array $authorization):string
    {
        if(count($authorization)){
            return explode("Bearer ", $authorization[0])[1];
        }

        return "";
    }

    private function parseToken(string $token): ?object
    {
        $decode = Encrypt::decode($token);

        if(is_null($decode)){
            return null;
        }

        return json_decode($decode);
    }

    public function generateToken(?InfosToken $infos):string
    {
        return Encrypt::encode(
            $infos instanceof InfosToken ? $infos->getString() : $this->InfosTokenInstance->getString()
        );
    }


    public function Login(string $email, string $password): array
    {
        $usuario = Usuario::where("email", $email)->first();

        if(!$usuario instanceof Usuario){
            return array(
                "success" => false,
                "error"   => ResponseMessage::getBadMessage(
                    array(),
                    "Não foi encontrado um usuario com esse email!"
                )
            );
        }

        if(!Hash::check($password, $usuario->password)) {
            return array(
                "success" => false,
                "error" => ResponseMessage::getBadMessage(
                    array(),
                    "Credenciais incorretas!"
                )
            );
        }

        DB::transaction(function() use($usuario, &$response) {

            $token = $this->generateToken($this->generateInfos($usuario->email));

            $this->UpdateToken($usuario, $token);

            $response = array(
                "success" => true,
                "data"    => ResponseMessage::getSuccessMessage(
                    array(
                        'token' => $token
                    ),
                    "Login efetuado com sucesso!"
                )
            );

        });

        return $response;
    }



    public function Me(HeaderBag $headers): ?Usuario
    {
        $tokenAr = $headers->all("authorization");

        $parseToken = $this->parseToken($this->getToken($tokenAr));

        if(is_null($parseToken))
        {
            return null;
        }

        $usuario = Usuario::where("email", $parseToken->email)->first();

        return $usuario;
    }


    public function Refresh(HeaderBag $headers): array
    {
        $tokenAr = $headers->all("authorization");

        $parseToken = $this->parseToken($this->getToken($tokenAr));

        $usuario = Usuario::where("email", $parseToken->email)->first();

        $newToken = $this->generateToken($this->generateInfos($parseToken->email));

        $this->UpdateToken($usuario, $newToken);

        return array(
            "success" => true,
            "data"    => ResponseMessage::getSuccessMessage(
                array(
                    'token' => $newToken
                ),
                "Token atualizado com sucesso!"
            )
        );
    }
}
