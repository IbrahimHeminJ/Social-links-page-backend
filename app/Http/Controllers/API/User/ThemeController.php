<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\User\ThemUpdateRequest;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\User\ThemeResource;
use App\Models\ThemePreset;
use App\Models\UserPage;
use Illuminate\Http\Request;

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
    public function update(ThemUpdateRequest $request)
    {
        $user = $request->user();
        
        // Get or create user's UserPage
        $userPage = $user->userPage;
        
        if (!$userPage) {
            // Create UserPage if it doesn't exist
            $userPage = UserPage::create([
                'user_id' => $user->id,
                'theme_id' => $request->validated()['theme_id'],
            ]);
        } else {
            // Update existing UserPage
            $userPage->update($request->validated());
        }
        
        // Load themePreset relationship
        $userPage->load('themePreset');
        
        return $this->success(
            'Theme updated successfully',
            new ThemeResource($userPage)
        );
    }
    public function destroy(Request $request)
    {
        $user = $request->user();
        $userPage = $user->userPage;
        
        if ($userPage) {
            $userPage->delete();
        }
        
        return $this->success(
            'Theme deleted successfully',
            null,
            204
        );
    }
}
