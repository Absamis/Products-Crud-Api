<?php

namespace App\Traits;

use App\Models\User;

trait HasUserAuth
{

    public function getValidLoginDetails()
    {
        $user = User::factory()->create();
        return $response = $this->postJson("/api/login", [
            "email" => $user->email,
            "password" => '123456',
        ]);
        $this->assertAuthenticated();
    }
}
