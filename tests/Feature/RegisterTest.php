<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Client as OClient;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_register(): void
    {
        $response = $this->post('api/register', [
            'first_name' => 'Imtiaz Ur',
            'last_name' => 'Rahman Khan',
            'email' => 'k.r.imtiaz@gmail.com',
            'password' => 'password',
        ]);
        $response->assertStatus(200);
    }



}
