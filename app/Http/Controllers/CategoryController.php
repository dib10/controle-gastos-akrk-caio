<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request) //pega o id do user pelo token e retorna as categorias
    {
        $categories = $this->categoryService->getAllForUser($request->user()->id);

        return response()->json($categories);
    
    }  
    
    public function store(Request $request) 
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $category = $this->categoryService->createCategory($validated, $request->user()->id);
        return response()->json($category, 201); //criado
    }

    public function destroy($id, Request $request) 
    {
        $deleted = $this->categoryService->deleteCategory($id, $request->user()->id);
        
        if (!$deleted) {
            return response()->json(['message' => 'Categoria não encontrada ou você não tem permissão para excluí-la.'], 403); 
        }

        return response()->json(['message' => 'Categoria excluída com sucesso']);
    }

        


        
}
