<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            "first_name" => "Chris",
            "last_name" => "Tian",
            "email" => "test@gmail.com",
            "phone" => "0814718232"
        ], [
            "Authorization" => "token"
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "first_name" => "Chris",
                    "last_name" => "Tian",
                    "email" => "test@gmail.com",
                    "phone" => "0814718232"
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            "first_name" => "",
            "last_name" => "Tian",
            "email" => "test@gmail.com",
            "phone" => "0814718232"
        ], [
            "Authorization" => "token"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "first_name" => [
                        "The first name field is required."
                    ]
                ]
            ]);
    }

    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            "first_name" => "",
            "last_name" => "Tian",
            "email" => "test@gmail.com",
            "phone" => "0814718232"
        ], [
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
