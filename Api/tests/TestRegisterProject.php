<?php

class TestRegisterProject extends TestCase
{
    protected $variables = [

        "success_data" =>  [
            "name"     => "laravel",
            "email"    => "joao.dev.lima@gmail.com",
            "password" => "admin"
        ],

        "error_data" => [

        ],

        "error_email_exist" => [
            "name"     => "laravel",
            "email"    => "joao.dev.lima@gmail.com",
            "password" => "admin"
        ],

        "name_error_fild" => [
            "email"    => "email@valido.com",
            "password" => "admin"
        ],

        "email_error_fild" => [
            "name"     => "laravel",
            "password" => "admin"
        ],

        "password_error_fild" => [
            "name"     => "laravel",
            "password" => "admin"
        ]

    ];

    private function getObjectVariable():object
    {
        return (object) $this->variables;
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPostCreateProject()
    {
        $this->json('POST', '/api/v1/auth/register' , $this->getObjectVariable()->success_data)
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'success',
                'message',
                'data' => [
                    "token",
                ]
            ]);
    }


    public function testePostErrorData()
    {
        $this->json('POST', '/api/v1/auth/register' , $this->getObjectVariable()->error_data)
            ->seeStatusCode(422)
            ->seeJsonStructure([
                "name",
                "email",
                "password",
            ]);
    }

    public function testePostErrorsFild()
    {
        $this->json('POST', '/api/v1/auth/register' , $this->getObjectVariable()->name_error_fild)
            ->seeStatusCode(422)
            ->seeJsonStructure([
                "name",
            ]);

        $this->json('POST', '/api/v1/auth/register' , $this->getObjectVariable()->email_error_fild)
            ->seeStatusCode(422)
            ->seeJsonStructure([
                "email",
            ]);

        $this->json('POST', '/api/v1/auth/register' , $this->getObjectVariable()->password_error_fild)
            ->seeStatusCode(422)
            ->seeJsonStructure([
                "email",
            ]);
    }



    public function testePostEmailExist()
    {
        $this->json('POST', '/api/v1/auth/register' , $this->getObjectVariable()->error_email_exist)
            ->seeStatusCode(422)
            ->seeJsonStructure([
                "email",
            ]);
    }





}
