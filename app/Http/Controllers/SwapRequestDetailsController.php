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
     * Swap List.
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
     * Display the specified resource.
     */
    public function show(SwapRequestDetail $swapRequestDetail)
    {
        try {
            return response()->json(['success' => true, 'data' => $swapRequestDetail], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve product'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SwapRequestDetail $swapRequestDetail)
    {
        return response()->json(['success' => true, 'data' => $swapRequestDetail], 200);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(SwapRequestDetail $swapRequestDetail)
    {
        $swapRequestDetail->delete();
        return response()->json(['success' => true, 'message' => 'Swap request details data deleted successfully'], 200);
    }
}
