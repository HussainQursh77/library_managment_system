<?php

namespace App\Services;

use App\Models\Rating;
use Exception;

class RatingService
{
    public function createRating($data)
    {
        try {
            return Rating::create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create rating: ' . $e->getMessage());
        }
    }

    public function updateRating(Rating $rating, $data, $userId)
    {
        try {
            if ($rating->user_id !== $userId) {
                throw new Exception('Unauthorized: You do not have permission to update this rating.');
            }
            $rating->update($data);
            return $rating;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteRating(Rating $rating, $userId)
    {
        try {
            if ($rating->user_id !== $userId) {
                throw new Exception('Unauthorized: You do not have permission to delete this rating.');
            }
            $rating->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }



}
