<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\User\UserButtonResource; 
use App\Models\Tag;
use App\Models\UserButton; 
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $tags = Tag::query()->with('users')
        ->where('tag', 'like', '%' . $search . '%')
        ->whereHas('users', function($query){ 
            $query->where('is_deleted',false);
        })
        ->get()->groupBy('tag');
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
