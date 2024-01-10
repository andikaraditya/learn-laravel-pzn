<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegisterSuccess()
    {
        $this->post("/api/users", [
            "username" => "Khannedy",
            "password" => "password",
            "name" => "Eko Khannedy",
        ])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => "Khannedy",
                    "name" => "Eko Khannedy",
                ]
            ]);
    }
    public function testRegisterFailed()
    {
        $this->post("/api/users", [
            "username" => "",
            "password" => "",
            "name" => "",
        ])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "The username field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ],
                ]
            ]);
    }
    public function testRegisterUsernameAlreadyExists()
    {
        $this->testRegisterSuccess();

        $this->post("/api/users", [
            "username" => "Khannedy",
            "password" => "password",
            "name" => "Eko Khannedy",
        ])
            ->assertStatus(400)
            ->assertJson([
                "error" => [
                    "username" => [
                        "username already exists"
                    ],
                ],
            ]);
    }
    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post("/api/login", [
            "username" => "test",
            "password" => "test",
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test",
                ]
            ]);

        $user = User::where("username", "test")->first();
        assertNotNull($user->token);
    }
    public function testLoginFail()
    {
        $this->post("/api/login", [
            "username" => "fail",
            "password" => "fail",
        ])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password is incorrect"
                    ]
                ]
            ]);
    }
    public function testLoginFailPasswordWrong()
    {
        $this->seed([UserSeeder::class]);

        $this->post("/api/login", [
            "username" => "test",
            "password" => "fail",
        ])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password is incorrect"
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->get("/api/users/current", [
            "Authorization" => "test"
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test",
                ]
            ]);
    }

    public function testGetFail()
    {
        $this->get("/api/users/current")
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }
    public function testGetFailInvalidToken()
    {
        $this->get("/api/users/current", [
            "Authorization" => "fail"
        ])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where("username", "test")->first();

        $this->patch(
            "/api/users/current",
            [
                "name" => "new",
                "password" => "new"
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(200);

        $newUser = User::where("name", "new")->first();

        self::assertNotEquals($newUser->name, $oldUser->name);
        self::assertNotEquals($newUser->password, $oldUser->password);
    }

    public function testUpdateFailNameTooLong()
    {
        $this->seed([UserSeeder::class]);

        $this->patch(
            "/api/users/current",
            [
                "name" => "7AZOjQ4OPBW9BenaGTckz6nxZvZ7AZOjQ4OPBW9BenaGTckz6nxZvZ7AZOjQ4OPBW9BenaGTckz6nxZvZ7AZOjQ4OPBW9BenaGTckz6nxZvZ7AZOjQ4OPBW9BenaGTckz6nxZvZ7AZOjQ4OPBW9BenaGTckz6nxZvZ",
                "password" => "new"
            ],
            [
                "Authorization" => "test"
            ]
        )
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }
}
