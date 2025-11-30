<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\userStoreRequest;
use App\Http\Requests\API\Admin\userUpdateRequest;
use App\Http\Resources\Admin\UserResource; 
use App\Http\Resources\CollectionResource;
use App\Models\User;
use App\Trait\ImageUpload;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ImageUpload;
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search by name or username
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%');
            });
        }
        
        $users = $query->paginate(10);
        return $this->success(
            'Users fetched successfully', 
            UserResource::collection( new CollectionResource($users))
        );
    } 
    public function store(userStoreRequest $request)
    {
        $user = User::create($request->validated());
        return $this->success(
            'User created successfully', 
            new UserResource($user)
        );
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        if(!$user){
            return $this->error('User not found', 404);
        }
        return $this->success(
            'User fetched successfully', 
            new UserResource($user)
        );
    }
    public function update(userUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);
        if(!$user){
            return $this->error('User not found', 404);
        }
        $user->update($request->validated());
        $user->tags()->sync($request->validated()['tags']);
        $user->profile_image = $this->updateImage($request, 'profile_image', 'users', $user->profile_image);
        $user->save();
        return $this->success(
            'User updated successfully', 
            new UserResource($user)
        );
    }
    public function destroy($id)
    {
        $user = User::query()->findOrFail($id);
        if(!$user){
            return $this->error('User not found', 404);
        }
        $this->deleteImage($user->profile_image);
        $user->save();
        $user->delete();
        return $this->success(
            'User deleted successfully', 
            null,
            204
        );
    }
}