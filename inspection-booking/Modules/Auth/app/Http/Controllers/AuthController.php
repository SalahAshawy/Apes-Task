<?php

namespace Modules\Auth\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\app\Http\Requests\RegisterRequest;
use Modules\Auth\app\Http\Requests\LoginRequest;
use Modules\Tenants\Entities\Tenant;
use Modules\Users\Entities\User;
use Illuminate\Support\Facades\DB;
use Modules\Auth\app\Http\Requests\RegisterUserRequest;

class AuthController extends Controller
{
    public function registerUser(RegisterUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'tenant_id' => $data['tenant_id'],
            'role'      => 'user',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $tenant = Tenant::create(['name' => $data['tenant_name']]);

            $user = User::create([
                'name'      => $data['admin_name'],
                'email'     => $data['admin_email'],
                'password'  => Hash::make($data['password']),
                'tenant_id' => $tenant->id,
                'role'      => 'admin'
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'tenant' => $tenant,
                'user'   => $user,
                'token'  => $token
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token
        ]);
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
