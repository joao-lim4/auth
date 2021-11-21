<?php

class TestLogin extends TestCase
{
    protected $variables = [

        "success_data" =>  [
            "email"    => "joao.dev.lima@gmail.com",
            "password" => "admin"
        ],

        "credencial_data" =>  [
            "email"    => "joao.dev.lima@gmail.com",
            "password" => "senha errada"
        ],

        "no_project_data" =>  [
            "email"    => "nao@gmail.com",
            "password" => "admin"
        ],

        "email_error_fild" => [
            "password" => "admin"
        ],

        "no_fild" => [

        ],

        "password_error_fild" => [
            "email" => "joao.dev.lima@gmail.com"
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
    public function testPostLogin()
    {
        $this->json('POST', '/api/v1/auth/login' , $this->getObjectVariable()->success_data)
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'success',
                'message',
                'data' => [
                    "token",
                ]
            ]);
    }

    public function testPostNoProject()
    {
        $this->json('POST', '/api/v1/auth/login' , $this->getObjectVariable()->no_project_data)
            ->seeStatusCode(400)
            ->seeJsonStructure([
                "error",
                "message",
                "errorData"
            ]);
    }

    public function testPostCredencial()
    {
        $this->json('POST', '/api/v1/auth/login' , $this->getObjectVariable()->credencial_data)
            ->seeStatusCode(400)
            ->seeJsonStructure([
                "error",
                "message",
                "errorData"
            ]);
    }

    public function testPostErrorsFild()
    {

        $this->json('POST', '/api/v1/auth/login' , $this->getObjectVariable()->email_error_fild)
            ->seeStatusCode(422)
            ->seeJsonStructure([
                "email",
            ]);

        $this->json('POST', '/api/v1/auth/login' , $this->getObjectVariable()->no_fild)
            ->seeStatusCode(422)
            ->seeJsonStructure([
                "email",
                "password"
            ]);

        $this->json('POST', '/api/v1/auth/login' , $this->getObjectVariable()->password_error_fild)
            ->seeStatusCode(422)
            ->seeJsonStructure([
                "password",
            ]);

    }

}
