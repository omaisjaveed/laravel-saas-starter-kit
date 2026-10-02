<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * Transform a member (user with role pivot) into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'role' => $this->whenPivotLoaded('organization_user', function () {
                return $this->pivot->role;
            }),
            'joined_at' => $this->whenPivotLoaded('organization_user', function () {
                return $this->pivot->created_at?->toIso8601String();
            }),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
