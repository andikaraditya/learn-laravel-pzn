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

        $user = User::where("username","test")->first();
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
}
