<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Log;
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

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            "first_name" => "Updated",
            "last_name" => "Updated",
            "email" => "test@gmail.com",
            "phone" => "0812345671"
        ], [
            "Authorization" => "token"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "Updated",
                    "last_name" => "Updated",
                    "email" => "test@gmail.com",
                    "phone" => "0812345671"
                ]
            ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            "first_name" => "",
            "last_name" => "Updated",
            "email" => "test@gmail.com",
            "phone" => "0812345671"
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

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete(uri: '/api/contacts/' . $contact->id, headers: [
            "Authorization" => "token"
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete(uri: '/api/contacts/' . ($contact->id + 1), headers: [
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

    public function testSearchByFirstName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=first', [
            "Authorization" => "token"
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(20, $response["meta"]["total"]);
    }

    public function testSearchByLastName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=last', [
            "Authorization" => "token"
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(20, $response["meta"]["total"]);
    }

    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=test', [
            "Authorization" => "token"
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(20, $response["meta"]["total"]);
    }

    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=111', [
            "Authorization" => "token"
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(20, $response["meta"]["total"]);
    }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=tidakada', [
            "Authorization" => "token"
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response["data"]));
        self::assertEquals(0, $response["meta"]["total"]);
    }

    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?size=5&page=2', [
            "Authorization" => "token"
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response["data"]));
        self::assertEquals(20, $response["meta"]["total"]);
        self::assertEquals(2, $response["meta"]["current_page"]);
    }
}
