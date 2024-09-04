<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'published_at',
        'category_id',

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Scope to filter by category name
    public function scopeFilterByCategory(Builder $query, $category)
    {
        if ($category) {

            return $query->whereHas('category', function ($query) use ($category) {
                $query->where('category', 'like', "%$category%");
            });
        }

        return $query;
    }

    // Scope to filter by author
    public function scopeFilterByAuthor(Builder $query, $author)
    {
        if ($author) {
            return $query->where('author', 'like', "%$author%");
        }

        return $query;
    }

    // Scope to filter by availability
    public function scopeAvailable(Builder $query)
    {
        return $query->whereDoesntHave('borrowRecords', function ($query) {
            $query->whereNull('returned_date');
        });
    }
}

