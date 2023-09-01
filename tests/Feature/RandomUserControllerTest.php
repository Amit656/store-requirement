<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RandomUserControllerTest extends TestCase
{
    public function test_it_fetches_and_returns_random_users()
    {
        // Mocking the external API response.
        Http::fake([
            'https://randomuser.me/api/' => Http::response([
                'results' => [
                    [
                        'name' => [
                            'first' => 'John',
                            'last' => 'Doe',
                        ],
                        'phone' => '123-456-7890',
                        'email' => 'johndoe@example.com',
                        'location' => [
                            'country' => 'USA',
                        ],
                    ],
                    // Add more user data here for testing.
                ],
            ]),
        ]);

        // Make a GET request to the controller method.
        $response = $this->get('/api/random-users');

        // Assert the response status code is 200 (OK).
        $response->assertStatus(200);

        // You can add more assertions to test other scenarios and edge cases.
    }
}
