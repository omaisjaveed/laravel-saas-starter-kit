<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo_url' => $this->logo_path ? asset(Storage::url($this->logo_path)) : null,
            'email' => $this->email,
            'website' => $this->website,
            'description' => $this->description,
            'owner' => new UserResource($this->whenLoaded('owner')),
            'users_count' => $this->when(isset($this->users_count), $this->users_count ?? null),
            'projects_count' => $this->when(isset($this->projects_count), $this->projects_count ?? null),
            'tasks_count' => $this->when(isset($this->tasks_count), $this->tasks_count ?? null),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
