<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Category;

class ExpenseService 
{
    //obtem todas as despesas do usuario
    public function getAllForUser($userId)
    {
        return Expense::with('category')->where('user_id', $userId)->orderBy('date', 'desc')->get();
    }

    //cria despesa
    public function createExpense(array $data, $userId)
    {
        $categoryExists = Category::where('id', $data['category_id'])->where('user_id', $userId)->exists(); // se a categoria existe e pertence ao usuario

        if (!$categoryExists)
    {
        return null; 
    }

        return Expense::create([
            'description' => $data['description'],
            'amount' => $data['amount'],
            'date' => $data['date'],
            'category_id' => $data['category_id'],
            'user_id' => $userId,
        ]); 
    }

    // atualiza a despesa
    public function updateExpense($expenseId, array $data, $userId)
    {
        $expense = Expense::where('id', $expenseId)->where('user_id', $userId)->first();

        if (!$expense) 
        {
            return null; // só pode atualziar se existir e pertencer ao user
        }

        if (isset($data['category_id'])) // ver se a categoria existe e pertence ao user
        {
            $categoryExists = Category::where('id', $data['category_id'])->where('user_id', $userId)->exists();

            if (!$categoryExists) 
            {
                return null; // trato no controller
            }
        }
        $expense->update($data);
        return $expense;

    }

    //exclui a despesa
    public function deleteExpense($expenseId, $userId)
    {
        $expense = Expense::where('id', $expenseId)->where('user_id', $userId)->first();

        if ($expense) 
        {
            $expense->delete();
            return true;
        }
        return false;
    }
}