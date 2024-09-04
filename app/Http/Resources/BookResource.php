<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'author' => $this->author,
            'description' => $this->description,
            'published_at' => $this->published_at,
            'category_name' => $this->category->category,
            'average_rating' => $this->average_rating,
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
        ];
    }
}
