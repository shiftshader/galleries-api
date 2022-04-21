<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only([
            'email', 'password'
        ]);

        // $user = User::where('email', $credentials['email'])->first();

        // if (!$user || !Hash::check($credentials['password'], $user->password)) {
        //     return response()->json([
        //         'message' => 'Invalid credentials'
        //     ], 401);
        // }

        // $token = Auth::login($user);
//!!!!!!!sve ovo gore se radi kroz klasu Auth::attempt($credentials);
        $token = Auth::attempt($credentials);
//provere da li postoji token
        if (!$token) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
        return response()->json([
            'token' => $token,
            'user' => Auth::user()
        ]);
    }
//dobavljanje profila
    public function getMyProfile()
    {
        //provera tokena za potpis, token, da li je istekao
        //dobavlja subject iz tokena i vrati nazad
        $activeUser = Auth::user();
        return response()->json($activeUser);
    }
///registracija
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        $token = Auth::login($user);

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }
//logout
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'user' => Auth::user()
        ]);
    }

    //refresh tokena
    public function refreshToken()
    {
        $newToken = Auth::refresh();
        return response()->json([
        'token' => $newToken
        ]);
    }
}
