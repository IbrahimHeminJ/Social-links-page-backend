<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
                'email_of_reporter' => $this->email_of_reporter,
                'title' => $this->title,
                'description' => $this->description,
                'report_type' => $this->report_type,
                'report_status' => $this->report_status,
                'handled_by' => new UserResource($this->user),
                'reason_of_action' => $this->reason_of_action,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'relations' => [
                'reported_on_user' => new UserResource($this->reportedOnUser),
            ],
        ];
    }
}
