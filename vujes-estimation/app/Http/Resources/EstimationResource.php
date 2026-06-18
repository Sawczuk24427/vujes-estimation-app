<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstimationResource extends JsonResource
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
            'title' => $this->title,
            'type' => $this->type,
            'hours' => $this->hours,
            'hourly-rate' => $this->hourly_rate,
            'price' => $this->price,
            'project' => new ProjectResource($this->whenLoaded('project')),
        ];
    }
}
