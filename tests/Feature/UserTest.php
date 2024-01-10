<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
}
