<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function store(UserRequest $request) {
        DB::beginTransaction();
        $user_role = Role::where('role_name', 'User')->first();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password
            ]);
            $user->roles()->attach($user_role->id);

            DB::commit();
            return new UserResource($user);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Saving an account failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([ 'error' => $e->getMessage, 'message' => 'Saving an account failed!' ], 500);
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid Credentials'], 401);
        }

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->load('roles');

            // Create token using Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Successfully logged in!',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        } else {
            return response()->json(['message' => 'Invalid Credentials'], 401);
        }
    }

    public function readNonAdmin() {
        $users = User::role('User')->get();
        return response()->json(['users' => $users], 200);
    }
}
