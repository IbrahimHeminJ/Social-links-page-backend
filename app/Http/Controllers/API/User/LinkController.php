<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\User\LinkButtonRequest;
use App\Http\Requests\API\User\LinkStoreRequest;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\User\ButtonLinkResource;
use App\Http\Resources\User\UserButtonResource;
use App\Models\ButtonLink;
use App\Models\UserButton;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $links = UserButton::query()
        ->where('user_id',$request->user()->id)
        ->with('buttonLink','user')
        ->orderBy('order','asc')
        ->get();
        return $this->success(
            'Links fetched successfully',
            UserButtonResource::collection(new CollectionResource($links))
        );
    }
    public function show($id)
    {
        $link = UserButton::findOrFail($id);
        return $this->success(
            'Link fetched successfully',
            UserButtonResource::collection(new CollectionResource($link))
        );
    }
    public function store(LinkButtonRequest $request)
    {
        $linkButton = ButtonLink::create($request->validated()['link']);
        UserButton::create([
            'user_id' => $request->user()->id,
            'button_id' => $linkButton->id,
            'order' => $request->validated()['order'],
        ]);
        return $this->success(
            'Link created successfully',
            null,
            201
        );
    }
    public function update(LinkButtonRequest $request, $id)
    {
        $link = UserButton::findOrFail($id);
        $link->update($request->validated());
        return $this->success(
            'Link updated successfully',
            null,
            200
        );
    }
    public function destroy($id)
    {
        $link = UserButton::findOrFail($id);
        $link->delete();
        return $this->success(
            'Link deleted successfully',
            null,
            204
        );
    }
}
