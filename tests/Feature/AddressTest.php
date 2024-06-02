<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
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

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [
            "Authorization" => "token"
        ])->assertStatus(200)
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

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1), [
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

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [
            "street" => "Update",
            "city" => "Update",
            "province" => "Update",
            "country" => "Update",
            "postal_code" => "3636",
        ], [
            "Authorization" => "token"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "street" => "Update",
                    "city" => "Update",
                    "province" => "Update",
                    "country" => "Update",
                    "postal_code" => "3636",
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [
            "street" => "Update",
            "city" => "Update",
            "province" => "Update",
            "country" => "",
            "postal_code" => "3636",
        ], [
            "Authorization" => "token"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "country" => [
                        "The country field is required."
                    ]
                ]
            ]);
    }

    public function testUpdateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1), [
            "street" => "Update",
            "city" => "Update",
            "province" => "Update",
            "country" => "Update",
            "postal_code" => "3636",
        ], [
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
}
