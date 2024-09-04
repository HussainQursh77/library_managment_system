<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\Request;
use Exception;

class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
        $this->bookService = $bookService;
    }

    /**
     * Display a listing of books with optional filters.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only([
                'category',
                'author',
                'available',
                'per_page',
                'order_by',
                'order_direction'
            ]);


            $books = $this->bookService->getBooks($filters);

            return response()->json([
                'data' => BookResource::collection($books)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve books: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        try {
            $validated = $request->validated();
            $book = $this->bookService->create($validated);
            return new BookResource($book);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create book: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * Display the specified book with its ratings.
     *
     * @param  int  $bookId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($bookId)
    {
        try {
            $book = $this->bookService->getBookWithRatings($bookId);
            return response()->json([
                'data' => new BookResource($book)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch book: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        try {
            $validated = $request->validated();
            $book = $this->bookService->update($book, $validated);
            return new BookResource($book);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update book: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        try {
            $currentUser = auth()->user();

            if ($currentUser && $currentUser->is_admin === 'admin') {
                $this->bookService->delete($book);
                return response()->json(null, 204);
            }

            return response()->json([
                'message' => 'Unauthorized: You do not have permission to delete this book.'
            ], 403);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete book: ' . $e->getMessage()], 500);
        }
    }
}
