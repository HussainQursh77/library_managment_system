<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BorrowRecord;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class BorrowService
{
    /**
     * Handle the borrowing logic.
     *
     * @param  array  $data
     * @return BorrowRecord
     * @throws Exception
     */
    public function borrowBook(array $data)
    {
        try {
            $book = Book::findOrFail($data['book_id']);

            // Check if the book is already borrowed and not returned
            $isBorrowed = BorrowRecord::where('book_id', $book->id)
                ->whereNull('returned_date')
                ->exists();

            if ($isBorrowed) {
                throw new Exception('The book is currently borrowed and not available.');
            }

            // Set the due_date to 14 days after borrowed_at if it's not provided
            if (empty($data['due_date'])) {
                $data['due_date'] = Carbon::parse($data['borrowed_at'])->addDays(14)->toDateString();
            }

            // Create a borrow record
            $borrowRecord = BorrowRecord::create([
                'book_id' => $data['book_id'],
                'user_id' => Auth::id(),
                'borrowed_at' => $data['borrowed_at'],
                'due_date' => $data['due_date'],
                'returned_date' => null,
            ]);

            return $borrowRecord;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Handle the book return logic.
     *
     * @param  int  $borrowRecordId
     * @return BorrowRecord
     * @throws Exception
     */
    public function returnBook(int $borrowRecordId)
    {
        try {
            $borrowRecord = BorrowRecord::findOrFail($borrowRecordId);

            $currentUser = Auth::user();
            if ($currentUser->id !== $borrowRecord->user_id && $currentUser->is_admin !== 'admin') {
                throw new Exception('Unauthorized: You do not have permission to return this book.');
            }

            $borrowRecord->returned_date = Carbon::now();
            $borrowRecord->save();

            return $borrowRecord;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
