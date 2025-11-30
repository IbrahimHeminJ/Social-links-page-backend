<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\TagReasource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
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
                'role' => $this->role,
            ],
        ];
    }
}
