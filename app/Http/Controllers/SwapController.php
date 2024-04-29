<?php

namespace App\Http\Controllers;

use App\Events\MessageBroadcast;
use App\Facades\StripePaymentFacade;
use App\Http\Requests\Swap\StoreSwapExchangeDetailsRequest;
use App\Http\Requests\Swap\StoreSwapRequest;
use App\Http\Requests\Swap\StoreSwapRequestDetails;
use App\Http\Requests\Swap\StoreSwapRequestDetailsRequest;
use App\Http\Requests\Swap\UpdateSwapDetailsRequest;
use App\Http\Requests\Swap\UpdateSwapRequest;
use App\Jobs\SwapJob;
use App\Models\Message;
use App\Models\Swap;
use App\Models\SwapExchangeDetails;
use App\Models\User;
use App\Services\SwapMessageService;
use App\Services\SwapNotificationService;
use App\Services\SwapRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwapController extends Controller
{
    const PER_PAGE = 10;
    const COMMISSION = 0.25;

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

        $swaps->where('requested_user_id', auth()->id());

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
     *          name="exchange_product[0][variation_size_id]",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(type="number", format="integer", example=1),
     *      ),
     *     @OA\Parameter(
     *          in="query",
     *          name="exchange_product[0][variation_color_id]",
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
    public function store(StoreSwapRequest                $swapRequest,
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

            $prepareData = SwapRequestService::prepareDetailsData(
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

            $conversation = SwapMessageService::createPrivateConversation(
                auth()->id(),
                $swapRequest->exchanged_user_id,
                'private',
                auth()->id(),
                'You have a new swap request ' . $swap->uid
            );

            $message = Message::create([
                'message' => 'You have a new swap request ' . $swap->id,
                'receiver_id' => $swapRequest->exchanged_user_id,
                'swap_id' => $swap->id,
                'sender_id' => auth()->id(),
                'conversation_id' => $conversation->id,
                'message_type' => 'notification',
            ]);

            $message = $message->load('sender', 'receiver', 'swap');

            dispatch(new SwapJob($swap, $conversation, $message));

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
        if ($swap->requested_user_id == auth()->id()) {

            return response()->json(['success' => true, 'data' => $swap]);
        }
        return response()->json(['success' => false, 'message' => 'You are not authorized to view this swap'], 401);


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
     *          name="exchange_product[0][variation_size_id]",
     *          required=true,
     *          description="Product ID",
     *          @OA\Schema(type="number", format="integer", example=1),
     *      ),
     *     @OA\Parameter(
     *          in="query",
     *          name="exchange_product[0][variation_color_id]",
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

            if (($swap->requested_user_id == auth()->id() || $swap->exchanged_user_id == auth()->id()) && $swap->status == 'accepted') {

                $prepareData = SwapRequestService::prepareDetailsData(
                    $SwapExchangeDetailsRequest,
                    $swap,
                    $SwapExchangeDetailsRequest->define_type
                );

                SwapExchangeDetails::insert($prepareData['insertData']);

                if ($updateSwapRequest->deleted_details_id) {
                    SwapRequestService::deleteDetailsData(
                        $updateSwapRequest->deleted_details_id,
                        $swap,
                        SwapRequestService::matchClass($SwapExchangeDetailsRequest->define_type)
                    );
                }

                $totalAmountAndCommission = SwapRequestService::calculateTotalAmountAndCommission(
                    $swap,
                    SwapRequestService::matchRelation($SwapExchangeDetailsRequest->define_type)
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
            }

            return response()->json(['success' => false, 'message' => 'You are not authorized to update this swap'], 401);

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

    /**
     * Swap Approve by user.
     *
     * @OA\Get(
     *     path="/api/swap-approve/{id}",
     *     tags={"Swaps"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Approve swap by ID",
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
    public function approve($id): \Illuminate\Http\JsonResponse
    {
        $swap = Swap::find($id);

        if ($swap->exchanged_user_id == auth()->id()) {

            $swap->update(['status' => 'accepted']);

            SwapNotificationService::sendNotification(
                $swap,
                [$swap->requested_user_id],
                'Swap request has been accepted'
            );

            return response()->json(['success' => true, 'message' => 'You accept the swap request'], 200);
        }

        return response()->json(['success' => true, 'message' => 'You are not allow to change the swap status'], 200);
    }

    /**
     * Swap Decline by user.
     *
     * @OA\Get(
     *     path="/api/swap-decline/{id}",
     *     tags={"Swaps"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Decline swap by ID",
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
    public function decline($id): \Illuminate\Http\JsonResponse
    {
        $swap = Swap::find($id);

        if ($swap->exchanged_user_id == auth()->id()) {

            $swap->update(['status' => 'decline']);

            SwapNotificationService::sendNotification(
                $swap,
                [$swap->requested_user_id],
                'your swap request has been declined'
            );

            return response()->json(['success' => true, 'message' => 'You decline the swap request'], 200);
        }

        return response()->json(['success' => true, 'message' => 'You are not allow to change the swap status'], 200);
    }

    public function complete($id): \Illuminate\Http\JsonResponse
    {
        $swap = Swap::find($id);

        $user = auth()->user()->load('activePaymentMethod');

        if (!$user->activePaymentMethod) {
            return response()->json(['success' => true, 'message' => 'You are not allow to change the swap status'], 200);
        }

        if ($swap->requested_total_commission < .50 || $swap->exchanged_total_commission < .50){
            return response()->json(['success' =>false , 'message'=> 'Minimum commission is 0.50'], 401);
        }

        if ($swap->requested_user_id == auth()->id()) {

            $swap->update(['is_approve_by_requester' => 1]);

            SwapNotificationService::sendNotification(
                $swap,
                [$swap->exchanged_user_id],
                'Swap request has been completed'
            );

            StripePaymentFacade::createInvoiceItem($user, $swap->requested_total_commission);


            if ($swap->is_approve_by_exchanger) {
                $swap->update(['status' => 'completed']);
            }

            return response()->json(['success' => true, 'message' => 'You complete the swap request'], 200);

        } elseif ($swap->exchanged_user_id == auth()->id()) {

            $swap->update(['is_approve_by_exchanger' => 1]);

            SwapNotificationService::sendNotification(
                $swap,
                [$swap->requested_user_id],
                'Swap request has been completed'
            );

            StripePaymentFacade::createInvoiceItem($user, $swap->exchanger_total_commission);

            if ($swap->is_approve_by_requester) {
                $swap->update(['status' => 'completed']);
            }

            return response()->json(['success' => true, 'message' => 'You complete the swap request'], 200);
        }

        return response()->json(['success' => true, 'message' => 'You are not allow to change the swap status'], 200);
    }
}
