<?php

namespace Modules\Auth\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Tenants\Entities\Tenant;
use Modules\Users\Entities\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'tenant_name' => 'required|string|max:255',
            'admin_name'  => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'password'    => 'required|min:6|confirmed',
        ]);

        \DB::beginTransaction();
        try {
            $tenant = Tenant::create(['name' => $data['tenant_name']]);

            $user = User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['password']),
                'tenant_id' => $tenant->id,
                'role' => 'admin'
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            \DB::commit();

            return response()->json([
                'tenant' => $tenant,
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['message'=>'Registration failed','error'=>$e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
