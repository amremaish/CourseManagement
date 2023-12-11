<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;

class CustomTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');

    }
    private function getSuccessRegister()
    {
        $userData = [
            'name' => 'Amr',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/signup', $userData);
        $response->assertStatus(201)
            ->assertJson(['message' => 'User registered successfully']);

    }
    public function getLoginToken()
    {
        $this->getSuccessRegister();
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);
        $jsonData = $response->json();

        $this->assertTrue(isset($jsonData['access_token']));
        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);
        return $jsonData['access_token'];
    }

}
