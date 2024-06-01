<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            "username" => "Tian",
            "password" => "r4h4s!a",
            "name" => "Christian"
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => "Tian",
                    "name" => "Christian",
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            "username" => "",
            "password" => "r4h4s!a",
            "name" => "Christian"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'username' => [
                        "The username field is required."
                    ],
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyExist()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            "username" => "Tian",
            "password" => "r4h4s!a",
            "name" => "Christian"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "username already registered"
                    ]
                ]
            ]);
    }
}
