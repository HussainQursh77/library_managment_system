<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Http\Requests\UpdateRatingRequest;
use App\Models\Rating;
use App\Services\RatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Resources\RatingResource;


class RatingController extends Controller
{
    protected $ratingService;
    /**
     * Constructor to initialize the RatingController.
     *
     * @param RatingService $ratingService The service responsible for handling business logic related to ratings.
     */
    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
        $this->middleware('auth:api');
    }


    /**
     * Store a newly created rating in storage.
     *
     * @param StoreRatingRequest $request The validated request containing rating data.
     * @return \Illuminate\Http\JsonResponse The JSON response with the newly created rating and status code 201.
     *
     * @throws \Exception If the rating creation fails.
     */
    public function store(StoreRatingRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['user_id'] = Auth::id();
            $rating = $this->ratingService->createRating($validated);
            return response()->json(new RatingResource($rating), 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    /**
     * Update the specified rating in storage.
     *
     * @param UpdateRatingRequest $request The validated request containing updated rating data.
     * @param Rating $rating The rating model instance to be updated.
     * @return \Illuminate\Http\JsonResponse The JSON response with the updated rating and status code 200.
     *
     * @throws \Exception If the user is not authorized or the update fails.
     */
    public function update(UpdateRatingRequest $request, Rating $rating)
    {
        try {
            $validated = $request->validated();
            $userId = Auth::id();
            $rating = $this->ratingService->updateRating($rating, $validated, $userId);
            return response()->json(new RatingResource($rating), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    /**
     * Remove the specified rating from storage.
     *
     * @param Rating $rating The rating model instance to be deleted.
     * @return \Illuminate\Http\JsonResponse The JSON response with status code 204 if successful.
     *
     * @throws \Exception If the user is not authorized to delete the rating.
     */

    public function destroy(Rating $rating)
    {
        try {
            $userId = Auth::id();
            $this->ratingService->deleteRating($rating, $userId);
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}
