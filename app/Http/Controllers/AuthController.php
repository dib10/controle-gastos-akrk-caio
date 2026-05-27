<?php

namespace App\Http\Controllers;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    //injeto a dependência do serviço de autenticação
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $result = $this->authService->register($validated);
        return response()->json([
            'user' => $result['user'],
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $result = $this->authService->login($credentials);

        if (!$result) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        return response()->json([
            'user' => $result['user'],
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
        ]);
    }
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return response()->json(['message' => 'Usuario deslogado com sucesso']);
    }
}
