<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserButtonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "order"=> $this->order,
            "relationship"=> [
                "button_link"=> new ButtonLinkResource($this->buttonLink),
                "user"=> new UserResource($this->user),
            ],
        ];
    }
}
