<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowRecordResource extends JsonResource
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
            'book_name' => $this->book->title,
            'user_email' => $this->user->email,
            'borrowed_at' => $this->borrowed_at,
            'due_date' => $this->due_date,
            'returned_date' => $this->returned_date,
        ];
    }
}
