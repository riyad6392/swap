<?php

namespace App\Http\Controllers;

use App\Http\Requests\Swap\StoreSwapExchangeDetailsRequest;
use App\Http\Requests\Swap\StoreSwapRequest;
use App\Http\Requests\Swap\StoreSwapRequestDetails;
use App\Http\Requests\Swap\StoreSwapRequestDetailsRequest;
use App\Http\Requests\Swap\UpdateSwapDetailsRequest;
use App\Http\Requests\Swap\UpdateSwapRequest;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Swap;
use App\Models\SwapExchangeDetails;
use App\Models\SwapRequestDetails;
use App\Models\User;
use App\Notifications\SwapRequestNotification;
use App\Services\SwapNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function index(Request $request): \Illuminate\Http\JsonResponse
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
     *         name="define_type",
     *         required=true,
     *         description="Exchanged Wholesale Amount",
     *         @OA\Schema(type="enum", example="exchange_product | request_product"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="exchange_product[0][product_id]",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="number", format="integer", example=1),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="exchange_product[0][variation_id]",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="number", format="integer", example=1),
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="exchange_product[0][variation_size]",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(type="number", format="integer", example=1),
     *      ),
     *     @OA\Parameter(
     *          in="query",
     *          name="exchange_product[0][variation_color]",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(type="number", format="integer", example=1),
     *      ),
     *     @OA\Parameter(
     *          in="query",
     *          name="exchange_product[0][variation_quantity]",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(type="number", format="integer", example=1),
     *      ),
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
    public function store(StoreSwapRequest $swapRequest,
                          StoreSwapExchangeDetailsRequest $SwapExchangeDetailsRequest): \Illuminate\Http\JsonResponse
    {
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

            $prepareData = $this->prepareDetailsData(
                $SwapExchangeDetailsRequest,
                $swap,
                $SwapExchangeDetailsRequest->define_type
            );

            SwapExchangeDetails::insert($prepareData['insertData']);

            $swap->update(
                [
                    'exchanged_wholesale_amount' => $prepareData['wholeSaleAmount'],
                    'exchanged_total_commission' => $prepareData['totalCommission'],
                ]
            );

            SwapNotificationService::sendNotification($swap);

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

    }

    /**
     * Edit Swap.
     *
     * @OA\Post (
     *     path="/api/swap/{id}",
     *     tags={"Swaps"},
     *     security={{ "apiAuth": {} }},
     *     summary="Create a new swap",
     *     @OA\Parameter(
     *         in="query",
     *         name="define_type",
     *         required=true,
     *         description="Exchanged Wholesale Amount",
     *         @OA\Schema(type="enum", example="exchange_product | request_product"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="exchange_product[0][product_id]",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="number", format="integer", example=1),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="exchange_product[0][variation_id]",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="number", format="integer", example=1),
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="exchange_product[0][variation_size]",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(type="number", format="integer", example=1),
     *      ),
     *     @OA\Parameter(
     *          in="query",
     *          name="exchange_product[0][variation_color]",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(type="number", format="integer", example=1),
     *      ),
     *     @OA\Parameter(
     *          in="query",
     *          name="exchange_product[0][variation_quantity]",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(type="number", format="integer", example=1),
     *      ),
     *          @OA\Parameter(
     *          in="query",
     *          name="define_type",
     *          required=true,
     *          description="Exchanged Wholesale Amount",
     *          @OA\Schema(type="enum", example="exchange_product | request_product"),
     *      ),
     *      @OA\Parameter(
     *          in="query",
     *          name="request_product[0][product_id]",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(type="number", format="integer", example=1),
     *      ),
     *      @OA\Parameter(
     *          in="query",
     *          name="request_product[0][variation_id]",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(type="number", format="integer", example=1),
     *      ),
     *      @OA\Parameter(
     *           in="query",
     *           name="request_product[0][variation_size]",
     *           required=true,
     *           description="Product ID",
     *           @OA\Schema(type="number", format="integer", example=1),
     *       ),
     *      @OA\Parameter(
     *           in="query",
     *           name="request_product[0][variation_color]",
     *           required=true,
     *           description="Product ID",
     *           @OA\Schema(type="number", format="integer", example=1),
     *       ),
     *      @OA\Parameter(
     *           in="query",
     *           name="request_product[0][variation_quantity]",
     *           required=true,
     *           description="Product ID",
     *           @OA\Schema(type="number", format="integer", example=1),
     *       ),
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
    public function update(UpdateSwapRequest        $updateSwapRequest,
                           UpdateSwapDetailsRequest $SwapExchangeDetailsRequest,
                           Swap                     $swap): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $prepareData = $this->prepareDetailsData(
                $SwapExchangeDetailsRequest,
                $swap,
                $SwapExchangeDetailsRequest->define_type
            );

            SwapExchangeDetails::insert($prepareData['insertData']);

            if ($updateSwapRequest->deleted_details_id) {
                $this->deleteDetailsData(
                    $updateSwapRequest->deleted_details_id,
                    $swap,
                    $this->matchClass($SwapExchangeDetailsRequest->define_type)
                );
            }

            $totalAmountAndCommission = $this->calculateTotalAmountAndCommission(
                $swap,
                $this->matchRelation($SwapExchangeDetailsRequest->define_type)
            );

            $swap->update(
                [
                    'exchanged_wholesale_amount' => (int)$prepareData['wholeSaleAmount'] +
                        (int)$totalAmountAndCommission['wholeSaleAmount'],
                    'exchanged_total_commission' => $prepareData['totalCommission'] +
                        (int)$totalAmountAndCommission['totalCommission'],
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
    public function destroy(Swap $swap): \Illuminate\Http\JsonResponse
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
                    'uid' => uniqid(),
                    'user_id' => auth()->id(),
                    'swap_id' => $swap->id,
                    'product_id' => $product['product_id'],
                    'product_variation_id' => $product['variation_id'],
                    'quantity' => $product['variation_quantity'],
                    'unit_price' => $variation->unit_price ?? 0,
                    'amount' => $product['variation_quantity'] * $variation->unit_price ?? 0,
                    'commission' => ($product['variation_quantity'] * $variation->unit_price ?? 0) * self::COMMISSION_PERCENTAGE,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ];
                $wholeSaleAmount += $product['variation_quantity'] * $variation->unit_price ?? 0;
                $totalCommission += ($product['variation_quantity'] * $variation->unit_price ?? 0) * self::COMMISSION_PERCENTAGE;
            }
        }
        return ['insertData' => $insertData, 'wholeSaleAmount' => $wholeSaleAmount, 'totalCommission' => $totalCommission];
    }

    protected function deleteDetailsData($deleted_id, $swap, $class): void
    {
        if (gettype($deleted_id) == 'string') $deleted_id = json_decode($deleted_id);

        $class::where('user_id', auth()->id())
            ->where('swap_id', $swap->id)
            ->whereIn('id', $deleted_id)
            ->delete();

    }

    protected function calculateTotalAmountAndCommission($swap, $relation): array
    {
        $wholeSaleAmount = 0;
        $totalCommission = 0;

        $detailsData = $swap->$relation; //relation

        foreach ($detailsData as $detail) {
            $wholeSaleAmount += $detail->amount;
            $totalCommission += $detail->commission;
        }

        return ['wholeSaleAmount' => $wholeSaleAmount, 'totalCommission' => $totalCommission];
    }

    protected function matchClass($define_type): string
    {
        return match ($define_type) {
            'exchange_product' => SwapExchangeDetails::class,
            'request_product' => SwapRequestDetails::class,
        };
    }

    public function matchRelation($define_type): string
    {
        return match ($define_type) {
            'exchange_product' => 'exchangeDetails',
            'request_product' => 'requestDetail',
        };
    }


}
