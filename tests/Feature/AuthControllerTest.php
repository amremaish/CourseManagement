<?php


namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSuccessRegister()
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

    public function testFailureRegisterValidation1()
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
    public function testFailureRegisterValidation2()
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
    public function testSuccessLogin()
    {
        $this->testSuccessRegister();
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
    public function testFailureLogin()
    {
        $this->testSuccessRegister();
        $loginData = [
            'email' => 'test123@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);
        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }
    /** @test */
    public function testFailureLoginValidation()
    {
        $this->testSuccessRegister();
        $response = $this->postJson('/api/auth/login', []);
        echo $response->content();
        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }


}
