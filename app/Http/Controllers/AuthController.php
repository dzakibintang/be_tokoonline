<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function profile()
    {
        return response()->json(Auth::user());
    }

    // ✅ REGISTER PELANGGAN
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'kata_sandi' => 'required|string|min:6',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'kata_sandi' => Hash::make($request->kata_sandi),
            'peran' => 'pelanggan',
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
        ]);

        return response()->json([
            'message' => 'Pendaftaran berhasil',
            'user' => $user
        ], 201);
    }

    // ✅ LOGIN
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'kata_sandi');

        // Mapping ke format yang sesuai karena field password kamu bernama 'kata_sandi'
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['kata_sandi'], $user->kata_sandi)) {
            return response()->json(['error' => 'Email atau kata sandi salah'], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user
        ]);
    }
}