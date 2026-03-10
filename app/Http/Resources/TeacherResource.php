<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\Teacher $resource
 */
class TeacherResource extends JsonResource
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
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'section_id' => $this->section_id,
            'section_name' => $this->section->section_name ?? null,
            'is_active' => $this->user->is_active ?? false,
            'is_admin' => $this->user->is_admin ?? false,
            'is_nonFaculty' => $this->user->is_nonFaculty ?? false,
            'user' => [
                'email' => $this->user->email ?? null,
                'avatar' => $this->user->avatar ?? null,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
