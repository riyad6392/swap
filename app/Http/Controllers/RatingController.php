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

    /**
     * Rating List.
     *
     * @OA\Get(
     *     path="/api/ratings",
     *     tags={"Ratings"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter(
     *          in="query",
     *          name="pagination",
     *          required=true,
     *          @OA\Schema(type="number"),
     *          example="10"
     *      ),
     *      @OA\Parameter(
     *          in="query",
     *          name="get_all",
     *          required=false,
     *          @OA\Schema(type="boolean")
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *           @OA\JsonContent(
     *               @OA\Property(property="data", type="json", example={}),
     *               @OA\Property(property="links", type="json", example={}),
     *               @OA\Property(property="meta", type="json", example={}),
     *           )
     *       ),
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
     */
    public function index(Request $request)
    {
        $rating = Rating::query();
        if ($request->get('get_all')) {
            return response()->json(['success' => true, 'data' => $rating->get()]);
        }
        $rating = $rating->paginate($request->pagination ?? self::PER_PAGE);

        return response()->json(['success' => true, 'data' => $rating]);
    }

    /**
     * @OA\Post(
     *     path="/api/ratings",
     *     tags={"Ratings"},
     *     security={{ "apiAuth": {} }},
     *     summary="Create a new rating",
     *     description="Create a new rating for a user.",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="ID of the user who is rating",
     *         required=true,
     *         @OA\Schema(type="integer", example=1),
     *     ),
     *     @OA\Parameter(
     *         name="rated_id",
     *         in="query",
     *         description="ID of the user being rated",
     *         required=true,
     *         @OA\Schema(type="integer", example=2),
     *     ),
     *     @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         description="Rating value assigned to the user (floating-point number)",
     *         required=true,
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example="3.5",
     *             minimum=0.5,
     *             maximum=5.0,
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="comments",
     *         in="query",
     *         description="Optional comments about the rating",
     *         required=false,
     *         @OA\Schema(type="string", example="Great user!"),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rating created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="user_id", type="integer", example=1),
     *                  @OA\Property(property="rated_id", type="integer", example=2),
     *                  @OA\Property(property="rating", type="number", format="float", example=3.5),
     *                  @OA\Property(property="comments", type="string", example="Great user!"),
     *              )
     *         ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Invalid user",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *          )
     *      ),
     * )
     */
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

    /**
     * Retrieve a specific rating.
     *
     * @OA\Get(
     *     path="/api/ratings/{id}",
     *     tags={"Ratings"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="get singe rating by rating id",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *    @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="errors", type="json", example={"message": {"Rating retrived successfully."}}),
     *         ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Invalid user",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *          )
     *      ),
     *     @OA\Response(
     *         response=404,
     *         description="Rating not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function show(Rating $rating)
    {
        try {
            return response()->json(['success' => true, 'data' => $rating], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve rating'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ratings/{id}",
     *     tags={"Ratings"},
     *     security={{ "apiAuth": {} }},
     *     summary="Rating updated successfully",
     *     description="Updated a rating for a user.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="get singe rating by rating id",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="ID of the user who is rating",
     *         required=true,
     *         @OA\Schema(type="integer", example=1),
     *     ),
     *     @OA\Parameter(
     *         name="rated_id",
     *         in="query",
     *         description="ID of the user being rated",
     *         required=true,
     *         @OA\Schema(type="integer", example=2),
     *     ),
     *     @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         description="Rating value assigned to the user (floating-point number)",
     *         required=true,
     *         @OA\Schema(
     *             type="number",
     *             format="float",
     *             example="3.5",
     *             minimum=0.5,
     *             maximum=5.0,
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="comments",
     *         in="query",
     *         description="Optional comments about the rating",
     *         required=false,
     *         @OA\Schema(type="string", example="Great user!"),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rating updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="user_id", type="integer", example=1),
     *                  @OA\Property(property="rated_id", type="integer", example=2),
     *                  @OA\Property(property="rating", type="number", format="float", example=4.5),
     *                  @OA\Property(property="comments", type="string", example="Great user!"),
     *              )
     *         ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Invalid user",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *          )
     *      ),
     * )
     */
    public function update(UpdateRatingRequest $updateRatingRequest, Rating $rating)
    {
        try {
            DB::beginTransaction();
            $rating = tap($rating)->update($updateRatingRequest->only([
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

    /**
     * Remove the specified rating from storage.
     *
     * @OA\Delete (
     *     path="/api/ratings/{id}",
     *     security={{ "apiAuth": {} }},
     *     tags={"Ratings"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the rating to be deleted",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Rating data deleted successfully")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Rating not found")
     *         ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Invalid user",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *          )
     *      ),
     * )
     */
    public function destroy(Rating $rating)
    {
        $rating->delete();
        return response()->json(['success' => true, 'message' => 'Rating data deleted successfully'], 200);
    }
}
