<?php

namespace App\Http\Controllers;

use App\Http\Requests\Swap\StoreSwapExchageDetails;
use App\Http\Requests\Swap\UpdateSwapExchangeDetails;
use App\Models\SwapExchangeDetail;

class SwapExchangeDetailsController extends Controller
{
    const PER_PAGE = 10;

    /**
     * Swap Exchange Details List.
     *
     * @OA\Get(
     *     path="/api/swap-exchange-details",
     *     tags={"Swap Exchange Details"},
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
        $swap_exchange_details = SwapExchangeDetail::paginate(self::PER_PAGE);
        return response()->json(['success' => true, 'data' => $swap_exchange_details]);
    }


    /**
     * Create Swap Exchange Details.
     *
     * @OA\Post (
     *     path="/api/swap-exchange-details",
     *     tags={"Swap Exchange Details"},
     *     security={{ "apiAuth": {} }},
     *     summary="Create a new swap exchange details",
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
     *               @OA\Property(property="errors", type="json", example={"message": {"Swap Exchange Details created successfully."}}),
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
    public function store(StoreSwapExchageDetails $storeSwapExchangeDetails)
    {
        try {
            DB::beginTransaction();

            $swap = SwapExchangeDetail::create($storeSwapExchangeDetails->only(
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
     * Retrieve a specific swap exchange details.
     *
     * @OA\Get(
     *     path="/api/swap-exchange-details/{id}",
     *     tags={"Swap Exchange Details"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="get single swap by swap exchange details id",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *    @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="errors", type="json", example={"message": {"Get swap exchange details."}}),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Swap exchange details not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Swap exchange details not found")
     *         )
     *     )
     * )
     */
    public function show(SwapExchangeDetail $SwapExchangeDetail)
    {
        try {
            return response()->json(['success' => true, 'data' => $SwapExchangeDetail], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve product'], 500);
        }
    }

    /**
     * Retrieve a specific swap exchange details.
     *
     * @OA\Get(
     *     path="/api/swap-exchange-details/{id}/edit",
     *     tags={"Swap Exchange Details"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="get single swap by swap exchange details id",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *    @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="errors", type="json", example={"message": {"Get swap exchange details."}}),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Swap exchange details not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Swap exchange details not found")
     *         )
     *     )
     * )
     */
    public function edit(SwapExchangeDetail $SwapExchangeDetail)
    {
        return response()->json(['success' => true, 'data' => $SwapExchangeDetail], 200);
    }

    /**
     * Update Swap Exchange Details.
     *
     * @OA\Post (
     *     path="/api/swap-exchange-details/{id}",
     *     tags={"Swap Exchange Details"},
     *     security={{ "apiAuth": {} }},
     *     summary="Update swap exchange details",
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
     *               @OA\Property(property="errors", type="json", example={"message": {"Swap Exchange Details updated successfully."}}),
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
    public function update(UpdateSwapExchangeDetails $updateSwapExchangeDetails, SwapExchangeDetail $SwapExchangeDetail)
    {
        try {
            DB::beginTransaction();
            $updateSwapExchangeDetails->update($updateSwapExchangeDetails->only(
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
            return response()->json(['success' => true, 'data' => $SwapExchangeDetail], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update swap'], 500);
        }
    }

    /**
     * Delete Swap Exchange Details.
     *
     * @OA\Delete (
     *     path="/api/swap-exchange-details/{id}",
     *     tags={"Swap Exchange Details"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Delete a swap exchange details by ID",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Swap exchage details and related data deleted successfully")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Swap exchange details not found")
     *         ),
     *     )
     * )
     */
    public function destroy(SwapExchangeDetail $SwapExchangeDetail)
    {
        $SwapExchangeDetail->delete();
        return response()->json(['success' => true, 'message' => 'Swap exchange details data deleted successfully'], 200);
    }
}
