<?php

use Illuminate\Support\Facades\Route;

// catch all envia todas as rotas para o frontend pro vue ser tratado pelo Vue Router, exceto as rotas que começam com /api
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');