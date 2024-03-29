<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rating\StoreRatingRequest;
use App\Http\Requests\Rating\UpdateRatingRequest;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    const PER_PAGE = 10;
    public function index(Request $request)
    {
        $rating =  Rating::query();
        if ($request->get('get_all')) {
            return response()->json(['success' => true, 'data' => $rating->get()]);
        }
        $rating = $rating->paginate($request->pagination ?? self::PER_PAGE);
        
        return response()->json(['success' => true, 'data' => $rating]);
    }

    public function store(StoreRatingRequest $storeRatingRequest)
    {
        try {
            DB::beginTransaction();
            $rating = Rating::create($storeRatingRequest->only(
                [
                    'user_id',
                    'rated_id',
                    'rating',
                    'comments'
                ]));
            DB::commit();

            return response()->json(['success' => true, 'data' => $rating], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Rating $rating)
    {
        try {
            return response()->json(['success' => true, 'data' => $rating], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve rating'], 500);
        }
    }

    public function update(UpdateRatingRequest $updateRatingRequest, Rating $rating)
    {
        try {
            DB::beginTransaction();
            $rating= tap($rating)->update($updateRatingRequest->only([
                'user_id',
                'rated_id',
                'rating',
                'comments'
            ]));
            DB::commit();

            return response()->json(['success' => true, 'data' => $rating], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Rating $rating)
    {
        $rating->delete();
        return response()->json(['success' => true, 'message' => 'Rating data deleted successfully'], 200);
    }
}
