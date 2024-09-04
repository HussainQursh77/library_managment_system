<?php

namespace App\Services;

use App\Models\Book;

class BookService
{

    /**
     * Get filtered and paginated list of books.
     *
     * @param  array  $filters
     * @return Book $book
     */
    public function getBooks(array $filters)
    {
        $categoryName = $filters['category'] ?? null;
        $author = $filters['author'] ?? null;
        $available = $filters['available'] ?? null;
        $perPage = $filters['per_page'] ?? 15;
        $orderBy = $filters['order_by'] ?? 'published_at';
        $orderDirection = strtolower($filters['order_direction']) ?? 'desc';

        $booksQuery = Book::query()
            ->with('category')
            ->filterByCategory($categoryName)
            ->filterByAuthor($author);

        if ($available) {
            $booksQuery->available();
        }

        $booksQuery->orderBy($orderBy, $orderDirection);
        $books = $booksQuery->paginate($perPage);
        $books->getCollection()->transform(function ($book) {
            $book->average_rating = $book->ratings()->avg('rating');
            return $book;
        });

        return $books;
    }

    /**
     * Create a new book.
     *
     * @param array $data
     * @return Book
     */
    public function create(array $data): Book
    {
        return Book::create($data);
    }

    /**
     * Get a book with its ratings.
     *
     * @param  int  $bookId
     * @return Book
     *
     */
    public function getBookWithRatings(int $bookId)
    {
        $book = Book::with('ratings')->findOrFail($bookId);
        $book->average_rating = $book->ratings->avg('rating');
        return $book;
    }

    /**
     * Update an existing book.
     *
     * @param Book $book
     * @param array $data
     * @return Book
     */
    public function update(Book $book, array $data): Book
    {
        $book->update($data);
        return $book;
    }

    /**
     * Delete a book.
     *
     * @param Book $book
     * @return void
     */
    public function delete(Book $book): void
    {
        $book->delete();
    }
}
