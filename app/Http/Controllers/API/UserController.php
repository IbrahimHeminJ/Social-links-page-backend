<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\TagReasource;
use App\Http\Resources\User\UserButtonResource; 
use App\Models\Tag;
use App\Models\UserButton; 
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getTags()
    {
        $tags = Tag::select('id', 'tag')->get();
        return $this->success(
            'Tags fetched successfully',
            $tags
        );
    }
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Tag::query()->with('users')
            ->whereHas('users', function($query) { 
                $query->where('is_deleted', false);
            });
        
        // Search by tag name, user name, or username
        if ($search) {
            $query->where(function($q) use ($search) {
                // Search by tag name
                $q->where('tag', 'like', '%' . $search . '%')
                  // OR search by user name or username
                  ->orWhereHas('users', function($userQuery) use ($search) {
                      $userQuery->where('is_deleted', false)
                                ->where(function($uq) use ($search) {
                                    $uq->where('name', 'like', '%' . $search . '%')
                                       ->orWhere('username', 'like', '%' . $search . '%');
                                });
                  });
            });
        }
        
        $tags = $query->get()->groupBy('tag');
        return $this->success(
            'Tags fetched successfully',
            $tags
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
