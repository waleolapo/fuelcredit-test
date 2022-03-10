<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\WelcomeRegisteredUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request) {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|max:25',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->notify(new WelcomeRegisteredUser($user));
    
            return response()->json([
                'data' => $user,
                'status' => 'success',
                'message' => 'User created successfully',
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e
            ], 500);
        }
        

    }

    /**
     * Login Method
     */
    public function login(Request $request) {

        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:5|max:25',
        ]);

        $user = User::whereEmail($request->email)->first();

        # Check for passweord match
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($user->name);

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'data' => $user
        ], 200);
    }

    public function logout() {

        auth()->user()->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful',
            'data' => null,
        ]);
    }
}
