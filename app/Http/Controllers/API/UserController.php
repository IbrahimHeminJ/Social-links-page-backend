<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\User\UserButtonResource; 
use App\Models\Tag;
use App\Models\User;
use App\Models\UserButton; 
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Load users with their userPage and themePreset
        $query = Tag::query()->with([
            'users' => function($query) {
                $query->where('is_deleted', false)
                      ->with('userPage.themePreset');
            }
        ])->whereHas('users', function($query) { 
            $query->where('is_deleted', false);
        });
        
        // Search by tag name, user name, username, or user ID
        if ($search) {
            $query->where(function($q) use ($search) {
                // Search by tag name
                $q->where('tag', 'like', '%' . $search . '%')
                  // OR search by user name, username, or ID
                  ->orWhereHas('users', function($userQuery) use ($search) {
                      $userQuery->where('is_deleted', false)
                                ->where(function($uq) use ($search) {
                                    // Check if search is numeric (user ID)
                                    if (is_numeric($search)) {
                                        $uq->where('id', $search);
                                    } else {
                                        // Search by name or username
                                        $uq->where('name', 'like', '%' . $search . '%')
                                           ->orWhere('username', 'like', '%' . $search . '%');
                                    }
                                });
                  });
            });
        }
        
        $tags = $query->get();
        
        // Format response to include theme information for users
        $formattedTags = $tags->groupBy('tag')->map(function($tagGroup) {
            return $tagGroup->map(function($tag) {
                return [
                    'id' => $tag->id,
                    'tag' => $tag->tag,
                    'created_at' => $tag->created_at,
                    'updated_at' => $tag->updated_at,
                    'users' => $tag->users->map(function($user) {
                        $userData = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'username' => $user->username,
                            'email' => $user->email,
                            'profile_image' => $user->profile_image ? (
                                filter_var($user->profile_image, FILTER_VALIDATE_URL) 
                                    ? $user->profile_image 
                                    : asset('storage/' . $user->profile_image)
                            ) : null,
                            'phone_no' => $user->phone_no,
                            'role' => $user->role,
                            'is_deleted' => $user->is_deleted,
                            'created_at' => $user->created_at,
                            'updated_at' => $user->updated_at,
                            'pivot' => $user->pivot,
                        ];
                        
                        // Add theme information if userPage exists
                        if ($user->userPage && $user->userPage->themePreset) {
                            $userData['theme'] = [
                                'id' => $user->userPage->id,
                                'theme_id' => $user->userPage->theme_id,
                                'theme_type' => $user->userPage->themePreset->theme_type,
                            ];
                        }
                        
                        return $userData;
                    }),
                ];
            });
        });
        
        return $this->success(
            'Tags fetched successfully',
            $formattedTags
        );
    }
    public function getTags()
    {
        $tags = Tag::query()->select('id', 'tag')->get();
        return $this->success(
            'Tags fetched successfully',
            $tags
        );
    }

    public function show($id)
    {
        $user = User::where('is_deleted', false)
            ->with('tags', 'userPage.themePreset')
            ->findOrFail($id);
        
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'profile_image' => $user->profile_image ? (
                filter_var($user->profile_image, FILTER_VALIDATE_URL) 
                    ? $user->profile_image 
                    : asset('storage/' . $user->profile_image)
            ) : null,
            'phone_no' => $user->phone_no,
            'role' => $user->role,
            'is_deleted' => $user->is_deleted,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'tags' => $user->tags->map(function($tag) {
                return [
                    'id' => $tag->id,
                    'tag' => $tag->tag,
                ];
            }),
        ];
        
        // Add theme information if userPage exists
        if ($user->userPage && $user->userPage->themePreset) {
            $userData['theme'] = [
                'id' => $user->userPage->id,
                'theme_id' => $user->userPage->theme_id,
                'theme_type' => $user->userPage->themePreset->theme_type,
            ];
        }
        
        return $this->success(
            'User fetched successfully',
            $userData
        );
    }

    public function getButtonLinks($id)
    {
        $buttonLinks = UserButton::with('buttonLink')->where('user_id', $id)->get();
        return $this->success(
            'Button links fetched successfully',
            UserButtonResource::collection(new CollectionResource($buttonLinks))
        );
    }

}
