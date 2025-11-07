<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        $data = $req->validate([
            'username' => ['required','string','max:80', 'unique:users,username'],
            'email'    => ['required','email','max:160', 'unique:users,email'],
            'password' => ['required','string','min:6'],
        ]);

        $user = User::create([
            'role'     => 'customer',
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'status'   => 'active',
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user'  => $user->only(['id','username','email','role','status']),
            'token' => $token,
        ], 201);
    }

    public function login(Request $req)
    {
        $data = $req->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return [
            'user'  => $user->only(['id','username','email','role','status']),
            'token' => $token,
        ];
    }

    public function me(Request $req)
    {
        return $req->user()->only(['id','username','email','role','status']);
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()?->delete();
        return ['ok' => true];
    }
}
