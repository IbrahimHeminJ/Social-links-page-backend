<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserReasource;
use App\Models\ThemePreset;
use App\Models\User;
use App\Models\UserPage;
use App\Trait\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log ; 

class AuthController extends Controller
{
    use ImageUpload;
    public function user(Request $request){
        return $this->success(
            'User fetched successfully', 
            new UserReasource($request->user())
        );
    }
    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $user->id . ',id',
            'email' => 'required|email|unique:users,email,' . $user->id . ',id',
            'phone_no' => 'required|digits:11',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
        ]);
        
        if ($request->hasFile('profile_image')) {
            // Delete old image and upload new one
            $validated['profile_image'] = $this->updateImage($request, 'profile_image', 'users', $user->profile_image);
        } else {
            unset($validated['profile_image']);
        }
        
        $user->update($validated);
        $user->tags()->sync($validated['tags']);
        return $this->success(
            'User updated successfully',
            new UserReasource($user)
        );
    }
    public function login(Request $request)
    {
        // Detect if input is email or username
        $loginField = filter_var($request->input('userCredential'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Validate based on detected field
        $validated = $request->validate([
            'userCredential' => 'required',
            'password' => 'required',
        ]);

        // Prepare credentials
        $credentials = [
            $loginField => $validated['userCredential'],
            'password' => $validated['password'],
        ];

        // Attempt login
        if (!Auth::attempt($credentials)) {
            return $this->error('Invalid credentials', 401);
        }

        // Get user
        $user = User::where($loginField, $validated['userCredential'])->first();

        // Token
        $token = $user->createToken(
            'auth_token '.$user->id,
            ['*'],
            now()->addMonth()
        )->plainTextToken;

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
        if ($request->hasFile('profile_image')) {
            $user->profile_image = $this->uploadImage($request, 'profile_image', 'users');
            $user->save();
        }
        $user->tags()->attach($validated['tags']);

        $token = $user->createToken('auth_token '.$user->id, ['*'], now()->addMonth())->plainTextToken;
        return $this->success(
            'Registration successful',
            [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserReasource($user->load('tags')),
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
