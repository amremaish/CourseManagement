<?php


namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function successRegister()
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

    /** @test */
    public function FailureRegisterValidation1()
    {
        $userData = [
            'name' => 'Amr',
            'email' => 'test@example.com'
        ];

        $response = $this->postJson('/api/auth/signup', $userData);
        $response->assertStatus(422)
            ->assertJson([
                'password' => ['The password field is required.']
            ]);

    }
    /** @test */
    public function FailureRegisterValidation2()
    {
        $userData = [
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/signup', $userData);
        $response->assertStatus(422)
            ->assertJson([
                'name' => ['The name field is required.'],
                'email' => ['The email field is required.']
            ]);

    }

    /** @test */
    public function successLogin()
    {
        $this->successRegister();
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

    /** @test */
    public function FailureLogin()
    {
        $this->successRegister();
        $loginData = [
            'email' => 'test123@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);
        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function FailureLoginValidation()
    {
        $this->successRegister();
        $response = $this->postJson('/api/auth/login', []);
        echo $response->content();
        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }


}
