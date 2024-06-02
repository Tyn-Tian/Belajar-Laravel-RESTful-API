<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            "street" => "Jl. Kambing Merah",
            "city" => "Pangkalpinang",
            "province" => "Pangkalpinang",
            "country" => "Indonesia",
            "postal_code" => "3636",
        ], [
            "Authorization" => "token"
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "street" => "Jl. Kambing Merah",
                    "city" => "Pangkalpinang",
                    "province" => "Pangkalpinang",
                    "country" => "Indonesia",
                    "postal_code" => "3636",
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            "street" => "Jl. Kambing Merah",
            "city" => "Pangkalpinang",
            "province" => "Pangkalpinang",
            "country" => "",
            "postal_code" => "3636",
        ], [
            "Authorization" => "token"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "country" => ["The country field is required."]
                ]
            ]);
    }

    public function testCreateContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/' . ($contact->id + 1) . '/addresses', [
            "street" => "Jl. Kambing Merah",
            "city" => "Pangkalpinang",
            "province" => "Pangkalpinang",
            "country" => "Indonesia",
            "postal_code" => "3636",
        ], [
            "Authorization" => "token"
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["not found"]
                ]
            ]);
    }
}