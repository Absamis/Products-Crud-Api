<?php

namespace Tests\Feature;

use App\Models\User;
use App\Traits\HasUserAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase, HasUserAuth;

    public function test_cannot_access_endpoint_without_valid_token()
    {
        $response = $this->withToken("sssss")->get("/api/products");
        $response->assertUnauthorized();
    }

    public function test_can_access_endpoint_with_valid_token()
    {
        $response = $this->getValidLoginDetails();
        $response = $this->withToken($response["data"]["access_token"])->get("/api/products");
        $response->assertOk();
    }
}
