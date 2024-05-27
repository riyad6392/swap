<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client as OClient;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class LoginTest extends TestCase
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

    public function test_login(): void
    {
        $this->withoutExceptionHandling();

        $provider = ["users", "admins"];
        $name = ["Users", "Admins"];

        $clients = new \Laravel\Passport\ClientRepository();


        for ($i = 0; $i < 2; $i++) {
            $clients->createPasswordGrantClient(
                null, $name[$i], 'http://localhost', $provider[$i]
            );
        }

        User::create([
            'first_name' => 'Imtiaz Ur',
            'last_name' => 'Rahman Khan',
            'email' => 'k.r.imtiaz@gmail.com',
            'password' => bcrypt('password'),

        ]);


        $client = OClient::where('password_client', 1)->where('provider', 'users')->first();

        $response = $this->post('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => 'k.r.imtiaz@gmail.com',
            'password' => 'password',
            'scope' => 'user'
        ]);
       // dd(1);

       // dd($response->getContent());

        $response->assertStatus(200);
    }
}
