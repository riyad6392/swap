<?php

namespace App\Http\Controllers;

use App\Http\Requests\Swap\StoreSwapRequest;
use App\Http\Requests\Swap\StoreSwapRequestDetails;
use App\Http\Requests\Swap\UpdateSwapRequestDetails;
use App\Models\Swap;
use App\Models\SwapRequestDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwapRequestDetailsController extends Controller
{
    const PER_PAGE = 10;

    /**
     * Swap Request Details List.
     *
     * @OA\Get(
     *     path="/api/swap-request-details",
     *     tags={"Swap Request Details"},
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
        $swap_request_details = SwapRequestDetail::paginate(self::PER_PAGE);
        return response()->json(['success' => true, 'data' => $swap_request_details]);
    }

    /**
     * Create Swap Request Details.
     *
     * @OA\Post (
     *     path="/api/swap-request-details",
     *     tags={"Swap Request Details"},
     *     security={{ "apiAuth": {} }},
     *     summary="Create a new swap request details",
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="swap_id",
     *         required=true,
     *         description="Swap ID",
     *         @OA\Schema(type="integer", example=1),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="user_id",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer", example=2),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="product_id",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=3),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="product_variation_id",
     *         required=true,
     *         description="Product Variation ID",
     *         @OA\Schema(type="integer", example=4),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="quantity",
     *         required=true,
     *         description="Quantity",
     *         @OA\Schema(type="integer", example=5),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="unit_price",
     *         required=true,
     *         description="Unit Price",
     *         @OA\Schema(type="number", format="double", example=10.50),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="amount",
     *         required=true,
     *         description="Amount",
     *         @OA\Schema(type="number", format="double", example=52.50),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="commission",
     *         required=true,
     *         description="Commission",
     *         @OA\Schema(type="number", format="double", example=5.25),
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Swap Request Details created successfully."}}),
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
    public function store(StoreSwapRequestDetails $storeSwapRequestDetails)
    {
        try {
            DB::beginTransaction();

            $swap = SwapRequestDetail::create($storeSwapRequestDetails->only(
                [
                    'swap_id',
                    'user_id',
                    'product_id',
                    'product_variation_id',
                    'quantity',
                    'unit_price',
                    'amount',
                    'commission',
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
     * Retrieve a specific swap request detail.
     *
     * @OA\Get(
     *     path="/api/swap-request-details/{id}",
     *     tags={"Swap Request Details"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="get single swap by swap request details id",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *    @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="errors", type="json", example={"message": {"Get swap request details."}}),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Swap request details not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Swap request details not found")
     *         )
     *     )
     * )
     */
    public function show(SwapRequestDetail $swapRequestDetail)
    {
        try {
            return response()->json(['success' => true, 'data' => $swapRequestDetail], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve data'], 500);
        }
    }

    /**
     * Edit a specific swap request detail.
     *
     * @OA\Get(
     *     path="/api/swap-request-details/{id}/edit",
     *     tags={"Swap Request Details"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="get single swap by swap request details id",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *    @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="errors", type="json", example={"message": {"Get swap request  details."}}),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Swap request details not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Swap request details not found")
     *         )
     *     )
     * )
     */
    public function edit(SwapRequestDetail $swapRequestDetail)
    {
        return response()->json(['success' => true, 'data' => $swapRequestDetail], 200);
    }

    /**
     * Update Swap Request Details.
     *
     * @OA\Post (
     *     path="/api/swap-request-details/{id}",
     *     tags={"Swap Request Details"},
     *     security={{ "apiAuth": {} }},
     *     summary="Update swap request details",
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="swap_id",
     *         required=true,
     *         description="Swap ID",
     *         @OA\Schema(type="integer", example=1),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="user_id",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer", example=2),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="product_id",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=3),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="product_variation_id",
     *         required=true,
     *         description="Product Variation ID",
     *         @OA\Schema(type="integer", example=4),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="quantity",
     *         required=true,
     *         description="Quantity",
     *         @OA\Schema(type="integer", example=5),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="unit_price",
     *         required=true,
     *         description="Unit Price",
     *         @OA\Schema(type="number", format="double", example=10.50),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="amount",
     *         required=true,
     *         description="Amount",
     *         @OA\Schema(type="number", format="double", example=52.50),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="commission",
     *         required=true,
     *         description="Commission",
     *         @OA\Schema(type="number", format="double", example=5.25),
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Swap Request Details updated successfully."}}),
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
    public function update(UpdateSwapRequestDetails $updateSwapRequestDetails, SwapRequestDetail $swapRequestDetail)
    {
        try {
            DB::beginTransaction();
            $updateSwapRequestDetails->update($updateSwapRequestDetails->only(
                [
                    'swap_id',
                    'user_id',
                    'product_id',
                    'product_variation_id',
                    'quantity',
                    'unit_price',
                    'amount',
                    'commission',
                ]
            ));

            DB::commit();
            return response()->json(['success' => true, 'data' => $swapRequestDetail], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update swap'], 500);
        }
    }

    /**
     * Delete Swap Request Details.
     *
     * @OA\Delete (
     *     path="/api/swap-request-details/{id}",
     *     tags={"Swap Request Details"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Delete a swap request details by ID",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Swap request details and related data deleted successfully")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Swap request details not found")
     *         ),
     *     )
     * )
     */
    public function destroy(SwapRequestDetail $swapRequestDetail)
    {
        $swapRequestDetail->delete();
        return response()->json(['success' => true, 'message' => 'Swap request details data deleted successfully'], 200);
    }
}
