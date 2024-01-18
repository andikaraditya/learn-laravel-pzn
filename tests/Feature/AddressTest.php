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

        $this->post(
            "/api/contacts/" . $contact->id . "/addresses",
            [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "test",
                "postal_code" => "12345",
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "street" => "test",
                    "city" => "test",
                    "province" => "test",
                    "country" => "test",
                    "postal_code" => "12345",
                ]
            ]);
    }

    public function testCreateFail()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->post(
            "/api/contacts/" . $contact->id . "/addresses",
            [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "",
                "postal_code" => "12345",
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(400)
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

        $this->post(
            "/api/contacts/" . ($contact->id + 1) . "/addresses",
            [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "test",
                "postal_code" => "12345",
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["not found"]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $address = Address::query()->limit(1)->first();

        $this->get(
            "/api/contacts/" . $contact->id . "/addresses/" . $address->id,
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "street" => "test",
                    "city" => "test",
                    "province" => "test",
                    "country" => "test",
                    "postal_code" => "12345",
                ]
            ]);
    }
    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $address = Address::query()->limit(1)->first();

        $this->get(
            "/api/contacts/" . $contact->id . "/addresses/" . ($address->id + 1),
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(404)
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

        $contact = Contact::query()->limit(1)->first();
        $address = Address::query()->limit(1)->first();

        $this->put(
            "/api/contacts/" . $contact->id . "/addresses/" . $address->id,
            [
                "street" => "update",
                "city" => "update",
                "province" => "update",
                "country" => "update",
                "postal_code" => "22222",
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "street" => "update",
                    "city" => "update",
                    "province" => "update",
                    "country" => "update",
                    "postal_code" => "22222",
                ]
            ]);
    }

    public function testUpdateFail()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $address = Address::query()->limit(1)->first();

        $this->put(
            "/api/contacts/" . $contact->id . "/addresses/" . $address->id,
            [
                "street" => "update",
                "city" => "update",
                "province" => "update",
                "postal_code" => "22222",
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "country" => ["The country field is required."]
                ]
            ]);
    }

    public function testUpdateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $address = Address::query()->limit(1)->first();

        $this->put(
            "/api/contacts/" . $contact->id . "/addresses/" . ($address->id + 1),
            [
                "street" => "update",
                "city" => "update",
                "province" => "update",
                "country" => "update",
                "postal_code" => "22222",
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["not found"]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $address = Address::query()->limit(1)->first();

        $this->delete(
            "/api/contacts/" . $contact->id . "/addresses/" . $address->id,
            [
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }
    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        $address = Address::query()->limit(1)->first();

        $this->delete(
            "/api/contacts/" . $contact->id . "/addresses/" . ($address->id +1),
            [
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["not found"]
                ]
            ]);
    }
}
