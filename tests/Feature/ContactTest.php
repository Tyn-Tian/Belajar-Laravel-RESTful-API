<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
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

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            "Authorization" => "token"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "Chris",
                    "last_name" => "Tian",
                    "email" => "test@gmail.com",
                    "phone" => "081234567"
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1), [
            "Authorization" => "token"
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            "Authorization" => "keren"
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }
}
