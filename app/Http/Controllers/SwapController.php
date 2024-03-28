<?php

namespace App\Http\Controllers;

use App\Http\Requests\Swap\StoreSwapExchangeDetailsRequest;
use App\Http\Requests\Swap\StoreSwapRequest;
use App\Http\Requests\Swap\StoreSwapRequestDetails;
use App\Http\Requests\Swap\UpdateSwapExchangeDetailsRequest;
use App\Http\Requests\Swap\UpdateSwapRequest;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Swap;
use App\Models\SwapExchangeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwapController extends Controller
{
    const PER_PAGE = 10;
    const COMMISSION_PERCENTAGE = 0.25;

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
    public function index(Request $request)
    {
        $swaps = Swap:: query();
        if ($request->get('get_all')) {
            return response()->json(['success' => true, 'data' => $swaps->get()]);
        }
        $swap = $swaps->paginate($request->pagination ?? self::PER_PAGE);
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
    public function store(StoreSwapRequest $swapRequest, StoreSwapExchangeDetailsRequest $SwapExchangeDetailsRequest): \Illuminate\Http\JsonResponse
    {
        dd($SwapExchangeDetailsRequest->all());
        try {
            DB::beginTransaction();

            $swap = Swap::create(
                [
                    'user_id' => auth()->id(),
                    'requested_user_id' => auth()->id(), // User who requested the swap
                    'exchanged_user_id' => $swapRequest->exchanged_user_id, // User who accepted the swap
                    'status' => $swapRequest->status,
                ]
            );

            $prepareData = $this->prepareDetailsData($SwapExchangeDetailsRequest, $swap, 'exchange_product');
            SwapExchangeDetail::insert($prepareData['insertData']);

            $swap->update(
                [
                    'exchanged_wholesale_amount' => $prepareData['wholeSaleAmount'],
                    'exchanged_total_commission' => $prepareData['totalCommission'],
                ]
            );

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
    public function update(UpdateSwapRequest                $updateSwapRequest,
                           UpdateSwapExchangeDetailsRequest $SwapExchangeDetailsRequest,
                           Swap                             $swap)
    {
        try {
            DB::beginTransaction();

//            $swap->update($updateSwapRequest->only(
//                [
//                    'requested_user_id',
//                    'exchanged_user_id',
//                    'status',
//                ]
//            ));

            $prepareData = $this->prepareDetailsData($SwapExchangeDetailsRequest, $swap, 'exchange_product');
            SwapExchangeDetail::insert($prepareData['insertData']);

            /// TODO: Need to delete the previous data
            $this->deleteDetailsData($updateSwapRequest->deleted_id, $swap);

            ///TODO: Need to update the total amount and commission
            $totalAmountAndCommission = $this->calculateTotalAmountAndCommission($swap);

            $swap->update(
                [
                    'exchanged_wholesale_amount' => $prepareData['wholeSaleAmount'] +
                        $totalAmountAndCommission['wholeSaleAmount'],
                    'exchanged_total_commission' => $prepareData['totalCommission'] +
                        $totalAmountAndCommission['totalCommission'],
                ]
            );

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
        if ($swap->user_id != auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to delete this swap'], 401);
        }
        $swap->exchangeDetails->delete();
        $swap->requestDetail->delete();
        $swap->delete();
        return response()->json(['success' => true, 'message' => 'Swap and related data deleted successfully'], 200);
    }

    protected function prepareDetailsData($request, object $swap, string $prepareFor): array
    {
        $insertData = [];
        $wholeSaleAmount = 0;
        $totalCommission = 0;
        foreach ($request->$prepareFor as $product) {
            $variation = ProductVariation::where('id', $product['variation_id'])
                ->where('product_id', $product['product_id'])
                ->first();

            if ($variation) {
                $insertData[] = [
                    'product_id' => $product['product_id'],
                    'product_variation_id' => $product['variation_id'],
                    'quantity' => $product['variation_quantity'],
                    'uid' => uniqid(),
                    'swap_id' => $swap->id,
                    'unit_price' => $variation->unit_price ?? 0,
                    'amount' => $product['variation_quantity'] *
                        $variation->unit_price ?? 0,
                    'commission' => ($product['variation_quantity'] *
                        $variation->unit_price ?? 0) *
                        self::COMMISSION_PERCENTAGE,
                    'user_id' => auth()->id(),
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ];
                $wholeSaleAmount += $product['variation_quantity'] *
                    $variation->unit_price ?? 0;
                $totalCommission += ($product['variation_quantity'] *
                    $variation->unit_price ?? 0) * self::COMMISSION_PERCENTAGE;
            }
        }
        return ['insertData' => $insertData, 'wholeSaleAmount' => $wholeSaleAmount, 'totalCommission' => $totalCommission];
    }


}
