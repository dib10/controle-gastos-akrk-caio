<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\ExpenseService;

class ExpenseController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    public function index(Request $request)
    {
        $expenses = $this->expenseService->getAllForUser($request->user()->id);
        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|gt:0', // maior que 0
            'date' => 'required|date|before_or_equal:tomorrow', // não pode lançar no futuro
            'category_id' => 'required|integer' 
        ]);

        $expense = $this->expenseService->createExpense($validated, $request->user()->id);

        if (!$expense) {
            return response()->json(['message' => 'Categoria ou despesa inválidas.'], 403);
        }

        return response()->json($expense, 201);
    }

    public function update(Request $request, $id)
    {
        //sometimes ta sendo usado como se fosse uma requisição  patch
        $validated = $request->validate([
            'description' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|gt:0',
            'date' => 'sometimes|required|date|before_or_equal:tomorrow',
            'category_id' => 'sometimes|required|integer'
        ]);

        $expense = $this->expenseService->updateExpense($id, $validated, $request->user()->id);

        if (!$expense) 
        {
            return response()->json(['message' => 'Despesa não encontrada, não pertence a você ou categoria inválida.'], 403);
        }

        return response()->json($expense);
    }

    public function destroy($id, Request $request)
    {
        $deleted = $this->expenseService->deleteExpense($id, $request->user()->id);

        if (!$deleted) 
        {
            return response()->json(['message' => 'Despesa não encontrada ou você não tem permissão para excluí-la.'], 403) ;
        }

        return response()->json(['message' => 'Despesa excluída com sucesso.']);
    }
}