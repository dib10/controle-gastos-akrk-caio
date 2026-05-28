<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class CategoryTest extends TestCase
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

    //Teste que cria uma categoria válida

    public function  test_user_can_create_a_valid_category()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']); //simula o login
        $response = $this->postJson('/api/categories', [
            'name' => 'Lazer',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'name' => 'Lazer',
                     'user_id' => $user->id,
                 ]);
    }
}
