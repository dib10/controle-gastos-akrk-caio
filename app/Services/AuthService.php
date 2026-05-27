<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
 {
    public function register(array $data) //recebo o array porque o controller vai me passar os dados do request
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null; // o escopo não caberia muita arquiteutra mas aqui eu pdoeria retornar uma exceção personalizada
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user)
    {
        //revogando o token
        $user->currentAccessToken()->delete();
    }



 }