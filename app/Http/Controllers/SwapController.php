<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSwapRequest;
use App\Http\Requests\UpdateSwapRequest;
use App\Models\Swap;
use Illuminate\Support\Facades\DB;

class SwapController extends Controller
{
    const PER_PAGE = 10;

    /**
     * Swap List.
     *
     * @OA\Get(
     *     path="/api/swap",
     *     tags={"Swaps"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="pagination",
     *          required=true,
     *
     *          @OA\Schema(type="number"),
     *          example="10"
     *      ),
     *
     *      @OA\Parameter(
     *          in="query",
     *          name="get_all",
     *          required=false,
     *
     *          @OA\Schema(type="boolean")
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="data", type="json", example={}),
     *               @OA\Property(property="links", type="json", example={}),
     *               @OA\Property(property="meta", type="json", example={}),
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
     */
    public function index()
    {
        $swap = Swap::paginate(self::PER_PAGE);
        return response()->json(['success' => true, 'data' => $swap]);
    }

    /**
     * Create Swap.
     *
     * @OA\Post (
     *     path="/api/swap",
     *     tags={"Swaps"},
     *     security={{ "apiAuth": {} }},
     *     summary="Create a new swap",
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="requested_user_id",
     *         required=true,
     *         description="Requested User ID",
     *         @OA\Schema(type="integer", example=2),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="exchanged_user_id",
     *         required=true,
     *         description="Exchanged User ID",
     *         @OA\Schema(type="integer", example=3),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="status",
     *         required=true,
     *         description="Status",
     *         @OA\Schema(type="string", example="pending"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="requested_wholesale_amount",
     *         required=true,
     *         description="Requested Wholesale Amount",
     *         @OA\Schema(type="integer", example=100),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="exchanged_wholesale_amount",
     *         required=true,
     *         description="Exchanged Wholesale Amount",
     *         @OA\Schema(type="integer", example=120),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="requested_total_commission",
     *         required=true,
     *         description="Requested Total Commission",
     *         @OA\Schema(type="number", format="double", example=10.5),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="exchanged_total_commission",
     *         required=true,
     *         description="Exchanged Total Commission",
     *         @OA\Schema(type="number", format="double", example=12.5),
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Swap created successfully."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *          )
     *      )
     * )
     */
    public function store(StoreSwapRequest $swapRequest)
    {
        try {
            DB::beginTransaction();

            $swap = Swap::create($swapRequest->only(
                [
                    'requested_user_id',
                    'exchanged_user_id',
                    'status',
                    'requested_wholesale_amount',
                    'exchanged_wholesale_amount',
                    'requested_total_commission',
                    'exchanged_total_commission',
                ]
            ));

            DB::commit();
            return response()->json(['success' => true, 'data' => $swap], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get swap by ID.
     *
     * @OA\Get(
     *     path="/api/swap/{id}",
     *     tags={"Swaps"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="get singe swap by swap id",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *    @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="errors", type="json", example={"message": {"Get swap details."}}),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Swap not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Swap not found")
     *         )
     *     )
     * )
     */
    public function show(Swap $swap)
    {
        return $swap;
    }

    /**
     * Update Swap.
     *
     * @OA\Post (
     *     path="/api/swap/{id}",
     *     tags={"Swaps"},
     *     security={{ "apiAuth": {} }},
     *     summary="Update an existing swap",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="ID of the swap to update",
     *         @OA\Schema(type="integer", example=1),
     *     ),
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="requested_user_id",
     *         required=true,
     *         description="Requested User ID",
     *         @OA\Schema(type="integer", example=2),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="exchanged_user_id",
     *         required=true,
     *         description="Exchanged User ID",
     *         @OA\Schema(type="integer", example=3),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="status",
     *         required=true,
     *         description="Status",
     *         @OA\Schema(type="string", example="pending"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="requested_wholesale_amount",
     *         required=true,
     *         description="Requested Wholesale Amount",
     *         @OA\Schema(type="integer", example=100),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="exchanged_wholesale_amount",
     *         required=true,
     *         description="Exchanged Wholesale Amount",
     *         @OA\Schema(type="integer", example=120),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="requested_total_commission",
     *         required=true,
     *         description="Requested Total Commission",
     *         @OA\Schema(type="number", format="double", example=10.5),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="exchanged_total_commission",
     *         required=true,
     *         description="Exchanged Total Commission",
     *         @OA\Schema(type="number", format="double", example=12.5),
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Swap updated successfully."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *          )
     *      ),
     *      @OA\Response(
     *         response=404,
     *         description="Swap not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Swap not found")
     *         )
     *     )
     * )
     */
    public function update(UpdateSwapRequest $updateSwapRequest, Swap $swap)
    {
        try {
            DB::beginTransaction();
            $swap->update($updateSwapRequest->only(
                [
                    'requested_user_id',
                    'exchanged_user_id',
                    'status',
                    'requested_wholesale_amount',
                    'exchanged_wholesale_amount',
                    'requested_total_commission',
                    'exchanged_total_commission',
                ]
            ));

            DB::commit();
            return response()->json(['success' => true, 'data' => $swap], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update swap'], 500);
        }
    }

    /**
     * Delete Swap.
     *
     * @OA\Delete (
     *     path="/api/swap/{id}",
     *     tags={"Swaps"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Delete a swap by ID",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Swap and related data deleted successfully")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Swap not found")
     *         ),
     *     )
     * )
     */
    public function destroy(Swap $swap)
    {
        $swap->delete();
        return response()->json(['success' => true, 'message' => 'Swap and related data deleted successfully'], 200);
    }
}
