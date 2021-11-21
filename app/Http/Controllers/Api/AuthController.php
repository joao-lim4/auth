<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\Request\ResponseMessage;
use App\Helpers\Auth\MainAuth;
use App\Models\Usuario;
use App\Models\MailConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    private function UpdateToken (Usuario $usuarioInstace, $token): void
    {
        DB::transaction(function() use($usuarioInstace, $token){
            $usuarioInstace->update([
                "token_access" => $token,
            ]);
        });
    }

    private function crateUser(array $data): array
    {

        DB::transaction(function () use($data, &$response){

            $newProject = Usuario::create([
                "name"     => $data["name"],
                "email"    => $data["email"],
                "password" => Hash::make($data["password"]),
                "nivel_id" => isset($data["nivel_id"]) ? $data["nivel_id"] : 1
            ]);


            $MainAuth = new MainAuth($newProject->email);

            $token = $MainAuth->generateToken(null);

            $this->UpdateToken($newProject, $token);

            $response = ResponseMessage::getSuccessMessage(
                array(
                    'token'        =>  $token,
                    'srecretToken' => $newProject->key
                ),
                "Usúario criado com sucesso!"
            );

        });

        return $response;
    }

    public function Register(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name'      => 'required',
            'email'     => 'required|email|unique:usuarios,email',
            'password'  => 'required',
        ]);

        $data = $request->all();

        $res = $this->crateUser($data);
        return response()->json($res, 200);
    }


    public function Login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email'      => 'required|email',
            'password'  => 'required'
        ]);

        $data = $request->all();

        $MainAuth = new MainAuth();

        $response = $MainAuth->Login($data["email"], $data["password"]);

        if($response["success"] === false) return response()->json($response["error"], 400);

        // $this->UpdateToken($response["projeto"], $response["token"]);

        return response()->json($response["data"], 200);
    }


    public function Me(Request $request): JsonResponse
    {
        $MainAuth = new MainAuth();

        $response = ResponseMessage::getSuccessMessage(
            array(
                'infos' => $MainAuth->me($request->headers)
            ),
            "Usuario autenticado"
        );


        return response()->json($response, 200);
    }


    public function Refresh(Request $request): JsonResponse
    {
        $MainAuth = new MainAuth();

        $response = $MainAuth->Refresh($request->headers);

        return response()->json($response, 200);
    }

}
