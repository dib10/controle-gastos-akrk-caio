<?php

namespace App\Services;

use App\Models\Category;

class CategoryService 
{

    //obtem todas as categorias do usuario
    public function getAllForUser($userId)
    {
        return Category::where('user_id', $userId)->get();
    }
    //cria uma nova categoria
    public function createCategory(array $data, $userId)
    {
        return Category::create([
            'name' => $data['name'],
            'user_id' => $userId,
        ]);
    }

    // atualiza a categoria
    public function updateCategory($categoryId, array $data, $userId)
    {
        $category = Category::where('id', $categoryId)->where('user_id', $userId)->first();

        if (!$category) {
            return null;
        }

        $category->update([
            'name' => $data['name'],
        ]);

        return $category;
    }

    //exclui a categoria
    public function deleteCategory($categoryId, $userId)
    {
        $category = Category::where('id', $categoryId)->where('user_id', $userId)->first();
        if ($category) //se existir e pertencer ao user
        {
            $category->delete();
            return true;
        }
        return false;
    }


    

}
