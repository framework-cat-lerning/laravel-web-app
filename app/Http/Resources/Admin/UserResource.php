<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property User $resource
 */
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
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'created_at' => $this->resource->created_at?->isoFormat('YYYY/MM/DD'),
            'updated_at' => $this->resource->updated_at?->isoFormat('YYYY/MM/DD'),
            'role' => [
                'id' => $this->resource->role,
                'label' => $this->resource->role->label(),
            ],
        ];
    }
}
