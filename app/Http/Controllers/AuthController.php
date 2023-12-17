<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'login' => 'required|unique:users',
            'password' => 'required|min:8|confirmed',
            'first_name' => 'required',
            'last_name' => 'required',
            'birthdate' => 'nullable|date'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);

        // Создаем токен для нового пользователя
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'error' => null,
            'result' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'token' => $token,
            ]
        ]);

    }

    public function login(Request $request)
    {
        // Попытка аутентификации пользователя
        if (!Auth::attempt($request->only('login', 'password'))) {
            return response()->json([
                'error' => 'Invalid login credentials'
            ], 401);
        }

        // Получение аутентифицированного пользователя
        $user = Auth::user();

        // Создание и возврат токена аутентификации
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'error' => null,
            'result' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'token' => $token
            ]
        ]);

    }

    public function show($user)
    {
        $user = User::find($user);
        return response()->json([
            'error' => null,
            'result' => $user
        ]);
    }
}
