<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowFormRequest;
use App\Services\BorrowService;
use Illuminate\Http\Request;
use Exception;
use App\Http\Resources\BorrowRecordResource;

class BorrowController extends Controller
{
    protected $borrowService;

    public function __construct(BorrowService $borrowService)
    {
        $this->middleware('auth:api');
        $this->borrowService = $borrowService;
    }

    /**
     * Borrow a book.
     *
     * @param  BorrowFormRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BorrowFormRequest $request)
    {
        try {
            $validated = $request->validated();

            \Log::info('Validated data: ', $validated);

            $borrowRecord = $this->borrowService->borrowBook($validated);

            return response()->json([
                'message' => 'Book borrowed successfully.',
                'data' => new BorrowRecordResource($borrowRecord)
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to borrow book: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Return a borrowed book.
     *
     * @param  int  $borrowRecordId
     * @return \Illuminate\Http\JsonResponse
     */
    public function returnBook($borrowRecordId)
    {
        try {
            $borrowRecord = $this->borrowService->returnBook($borrowRecordId);

            return response()->json([
                'message' => 'Book returned successfully.',
                'data' => new BorrowRecordResource($borrowRecord)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to return book: ' . $e->getMessage()
            ], 400);
        }
    }
}
