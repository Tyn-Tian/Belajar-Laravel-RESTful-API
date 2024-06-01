<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
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
        $this->seed([UserSeeder::class]);
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

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            "username" => "Tian",
            "password" => "r4h4s!a"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "Tian",
                    "name" => "Christian",
                ]
            ]);

        $user = User::where("username", "Tian")->first();
        self::assertNotNull($user->token);
    }

    public function testLoginUsernameNotFound()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            "username" => "Rawr",
            "password" => "r4h4s!a"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    public function testLoginWrongPassword()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            "username" => "Tian",
            "password" => "gatau"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            "Authorization" => "token"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "Tian",
                    "name" => "Christian"
                ]
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorize"
                    ]
                ]
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            "Authorization" => "gatau"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorize"
                    ]
                ]
            ]);
    }
}
