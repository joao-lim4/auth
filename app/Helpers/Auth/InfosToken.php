<?php

namespace App\Helpers\Auth;


class InfosToken {

    protected $infos = [
        "email"   => null,
        "timeout" => null,
        "age"     => null,
        "isValid" => 1
    ];

    protected $config;

    public function __construct(string $email)
    {
        if(strpos($email, "@"))
        {
            if($this->setInfos("email", $email) === FALSE)
            {
                $this->infos["email"] = null;
            }

        }

        $this->config = [
            "age" => env("AUTHENTICATE_AGE", 1),
            "timeout" => env("AUTHENTICATE_TIMEOUT", 86400)
        ];

        $this->setTimeout();
        $this->setAge();
    }



    private function setInfos(string $key, string $value): bool
    {
        if(array_key_exists($key, $this->infos))
        {
            $this->infos[$key] = $value;
            return true;
        }

        return false;
    }


    private function getConfig(string $key):string
    {

        if(array_key_exists($key, $this->infos) === TRUE)
        {
            return $this->config[$key];
        }

        return "";
    }


    public function getInfos()
    {
        return $this->infos;
    }

    private function parseSecunds(int $secunds):int
    {
        if($secunds < 84600){
            return 86400;
        }

        return ((int) ($secunds / 86400) * (int) $this->getConfig("age"));
    }

    private function generateTimeout(int $incrementDays): string
    {
        $dateAt = date('Y-m-d');
        return date('Y-m-d', strtotime('+' . $incrementDays . 'days', strtotime($dateAt)));
    }

    private function setTimeout():void
    {
        if(is_array($this->config) === TRUE)
        {
            $timeout = $this->generateTimeout($this->parseSecunds($this->getConfig("timeout")));
            $newDateAtual = date('Y-m-d');

            $this->setInfos("timeout", $timeout);
        }
    }

    private function setAge():void
    {
        if(is_array($this->config) === TRUE)
        {
            $this->setInfos("age", $this->getConfig("age"));
        }
    }


    public function getString()
    {
        return json_encode($this->infos);
    }
}
