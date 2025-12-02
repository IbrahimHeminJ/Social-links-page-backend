<?php

namespace App\Http\Resources;

use App\Http\Resources\User\ThemeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserReasource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'data' => [
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'profile_image' => asset('storage/' . $this->profile_image),
                'phone_no' => $this->phone_no,
                'tags' => TagReasource::collection($this->tags),
                'role' => $this->when(
                    $request->route()->getName() === 'auth.user',
                    $this->role,
                ),
                'premium' => $this->premium,
                'theme' => $this->when(
                    $this->userPage,
                    new ThemeResource($this->userPage->load('themePreset'))
                ),
            ],
        ];
    }
}
