<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Expense;
use Laravel\Sanctum\Sanctum;

class ExpenseTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // caminho feliz: criar despesa válida
    public function test_user_can_create_a_valid_expense()
    {
        $user = User::factory()->create();

        $category = $category = Category::create(['name' => 'Lazer', 'user_id' => $user->id
]);
        Sanctum::actingAs($user, ['*']); // simula o login
        $response = $this->postJson('/api/expenses', [
            'description' => 'Jogo do Corinthians',
            'amount' => 110,
            'date' => '2026-03-27',
            'category_id' => $category->id,
        ]);     

        $response->assertStatus(201);
        $this->assertDatabaseHas('expenses', ['description' => 'Jogo do Corinthians']);
    }

    //caminho feliz; excluir propia despesa
    public function test_user_can_delete_own_expense()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Lazer', 'user_id' => $user->id]);
        $expense = Expense::create([
            'description' => 'Jogo do Corinthians',
            'amount' => 110,
            'date' => now()->format('Y-m-d'),
            'category_id' => $category->id,
            'user_id' => $user->id,
        ]);
        Sanctum::actingAs($user, ['*']); // simula o login
        $response = $this->deleteJson("/api/expenses/{$expense->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }


    //caminho triste: bloquear valor negativo ou zero
    public function test_expense_amount_must_be_greater_than_zero()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Lazer', 'user_id' => $user->id]);
        Sanctum::actingAs($user, ['*']); // simula o login

        $response = $this->postJson('/api/expenses', [
            'description' => 'Cinema',
            'amount' => -1.00, 
            'date' => now()->format('Y-m-d'),
            'category_id' => $category->id,
        ]);

        $response->assertStatus(422);
    }

    //caminho triste: bloquear data futura
    public function test_expense_date_cannot_be_in_the_future()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Lazer', 'user_id' => $user->id]);
        Sanctum::actingAs($user, ['*']); // simula o login

        $response = $this->postJson('/api/expenses', [
            'description' => 'Jogo do Corinthians',
            'amount' => 110.00, 
            'date' => now()->addDay(5)->format('Y-m-d'), 
            'category_id' => $category->id,
        ]);

        $response->assertStatus(422);
    }

    //caminho triste: bloquear categoria de outro usuario
    public function test_user_cannot_use_category_of_another_user()
   {
        $user1 = User::factory()->create();
        $category = Category::create(['name' => 'Educação', 'user_id' => $user1->id]);
        $expense = Expense::create([
            'description' => 'Cursinho de php',
            'amount' => 140.00,
            'date' => now()->format('Y-m-d'),
            'category_id' => $category->id,
            'user_id' => $user1->id,
        ]);


        $user2 = User::factory()->create();
        Sanctum::actingAs($user2);

        $response = $this->deleteJson("/api/expenses/{$expense->id}");
        $response->assertStatus(403); 
        $this->assertDatabaseHas('expenses', ['id' => $expense->id]); 
    }

}
