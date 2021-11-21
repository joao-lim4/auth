<?php

use App\Models\Projeto;

class TestMiddleware extends TestCase
{
    protected $variables = [

        "user" =>  [
            "email"    => "joao.dev.lima@gmail.com",
            "password" => "admin"
        ],

        "http_authorize_bad" => [

        ]

    ];

    private function getObjectVariable():object
    {
        return (object) $this->variables;
    }


    private function getToken():string
    {
        return Projeto::where("email", $this->getObjectVariable()->user["email"])->first()->token_access;
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRequestAuth()
    {
        $this->json('GET', '/api/v1/authenticate/me', [], [
            "Authorization" => "Bearer " . $this->getToken()
        ])->seeStatusCode(200);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRequestNoAuth()
    {
        $this->json('GET', '/api/v1/authenticate/me', [], $this->getObjectVariable()->http_authorize_bad)
            ->seeStatusCode(401);
    }
}
