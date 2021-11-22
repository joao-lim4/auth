<?php


namespace App\Helpers\Request;

interface IResponseMessage {
    public function getSuccessMessage(array $data, ?string $message): array;
    public function getBadMessage(?string $message): array;
}

class ResponseMessage {

    protected static $successMessage = [
        "default" => [
            "_" => "Request feito com sucesso!"
        ],
    ];

    protected static $badMessage = [
        "default" => [
            "_" => "Algo de errado aconteceu, tente novamente mais tarde!"
        ],
    ];

    public static function getSuccessMessage(array $data, ?string $message): array
    {
        return array(
            "success" => true,
            "message" => $message ? $message : self::$successMessage["default"]["_"],
            "data"    => $data
        );
    }


    public static function getBadMessage(array $error, ?string $message): array
    {
        return array(
            "error"     => true,
            "message"   => $message ? $message : self::$badMessage["default"]["_"],
            "errorData" => $error
        );
    }

}
