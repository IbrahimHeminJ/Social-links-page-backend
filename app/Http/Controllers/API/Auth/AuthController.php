<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserReasource;
use App\Models\ThemePreset;
use App\Models\User;
use App\Models\UserPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function user(Request $request){
        return $this->success(
            'User fetched successfully', 
            new UserReasource($request->user())
        );
    }
    public function update(Request $request)
    {
        $userId = $request->route('id');
        $user = User::findOrFail($userId);
        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users,email,' . $user->id.',id',
            'phone_no' => 'required|digits:11',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
        ]);
        $user = $request->user();
        $user->update($validated);
        $user->tags()->attach($validated['tags']);
        return $this->success(
            'User updated successfully',
            new UserReasource($user)
        );
    }
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        
        if (!Auth::attempt($validated)) {
            return $this->error('Invalid credentials', 401);
        }
        
        $user = User::where('email', $validated['email'])->first();
        $token = $user->createToken('auth_token '.$user->id, ['*'], now()->addMonth())->plainTextToken;
        return $this->success(
            'Login successful',
            [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserReasource($user),
            ],
            200
        );
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone_no' => 'required|digits:11',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $user = User::create($validated);
        UserPage::create([
            'user_id' => $user->id,
            'theme_id' => ThemePreset::first()->id,
        ]);
        $user->tags()->attach($validated['tags']);

        $token = $user->createToken('auth_token '.$user->id, ['*'], now()->addMonth())->plainTextToken;
        return $this->success(
            'Registration successful',
            [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserReasource($user),
            ],
            200
        );
        
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(
            'Logged out',
            null,
            200
        );
    }

}
