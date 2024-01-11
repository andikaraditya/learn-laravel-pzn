<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            "/api/contacts",
            [
                "first_name" => "Eko",
                "last_name" => "Khan",
                "email" => "eko@mail.com",
                "phone" => "0894756595",
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(201)
            ->assertJson(
                [
                    "data" => [
                        "first_name" => "Eko",
                        "last_name" => "Khan",
                        "email" => "eko@mail.com",
                        "phone" => "0894756595",
                    ]
                ]
            );
    }

    public function testCreateFail()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            "/api/contacts",
            [
                "first_name" => "",
                "last_name" => "Khan",
                "email" => "eko",
                "phone" => "0894756595",
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(400)
            ->assertJson(
                [
                    "errors" => [
                        "first_name" => [
                            "The first name field is required."
                        ],
                        "email" => [
                            "The email field must be a valid email address."
                        ],
                    ]
                ]
            );
    }

    public function testCreateFailUnaithorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            "/api/contacts",
            [
                "first_name" => "",
                "last_name" => "Khan",
                "email" => "eko",
                "phone" => "0894756595",
            ],
        )
            ->assertStatus(401)
            ->assertJson(
                [
                    "errors" => [
                        "message" => [
                            "unauthorized"
                        ]
                    ]
                ]
            );
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/" . $contact->id, [
            "Authorization" => "test"
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "test",
                    "last_name" => "test",
                    "email" => "test@mail.com",
                    "phone" => "123456"
                ]
            ]);
    }
    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/" . ($contact->id + 1), [
            "Authorization" => "test"
        ])
            ->assertStatus(404)
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

        $this->get("/api/contacts/" . $contact->id, [
            "Authorization" => "test2"
        ])
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
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put(
            "/api/contacts/" . $contact->id,
            [
                "first_name" => "updated",
                "last_name" => "updated",
                "email" => "updated@mail.com",
                "phone" => "123456789",
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(200)
            ->assertJson(
                [
                    "data" => [
                        "first_name" => "updated",
                        "last_name" => "updated",
                        "email" => "updated@mail.com",
                        "phone" => "123456789",
                    ]
                ]
            );
    }

    public function testUpdateFail()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put(
            "/api/contacts/" . $contact->id,
            [
                "first_name" => "",
                "last_name" => "updated",
                "email" => "updated@mail.com",
                "phone" => "123456789",
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(400)
            ->assertJson(
                [
                    "errors" => [
                        "first_name" => [
                            "The first name field is required."
                        ],
                    ]
                ]
            );
    }
}
