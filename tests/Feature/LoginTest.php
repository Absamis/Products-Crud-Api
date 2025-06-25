<?php

namespace Tests\Feature;

use App\Models\User;
use App\Traits\HasUserAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase, HasUserAuth;

    public function test_login_with_valid_details()
    {
        $response = $this->getValidLoginDetails();
        $response->assertJsonStructure(["data" => ["access_token"]]);
    }

    public function test_get_access_token_after_success_login()
    {
        $response = $this->getValidLoginDetails();
        $response->assertJsonStructure(["data" => ["access_token"]]);
    }

    public function test_cannot_login_with_invalid_details()
    {
        $response = $this->postJson("/api/login", [
            "email" => "bad@example.com",
            "password" => "122"
        ]);
        $response->assertUnauthorized();
    }
}
