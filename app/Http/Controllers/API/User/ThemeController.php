<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\User\ThemUpdateRequest;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\User\ThemeResource;
use App\Models\ThemePreset;
use App\Models\UserPage;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = ThemePreset::query()->get();
        return $this->success(
            'Themes fetched successfully',
            ThemeResource::collection(new CollectionResource($themes))
        );
    }
    public function update(ThemUpdateRequest $request, $id)
    {
        $userPage = auth()->user()->userPage()->find($id);
        if(!$userPage){
            return $this->error('User page not found', 404);
        }
        $userPage->update($request->validated());
        return $this->success(
            'Theme updated successfully',
            new ThemeResource($userPage)
        );
    }
    public function destroy($id)
    {
        $userPage = UserPage::find($id);
        if(!$userPage){
            return $this->error('User page not found', 404);
        }
        $userPage->delete();
        return $this->success(
            'Theme deleted successfully',
            null,
            204
        );
    }
}
