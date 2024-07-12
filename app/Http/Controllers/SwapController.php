<?php

namespace App\Http\Controllers;

use App\Events\MessageBroadcast;
use App\Facades\StripePaymentFacade;
use App\Facades\NotificationFacade;
use App\Http\Requests\Swap\StoreSwapExchangeDetailsRequest;
use App\Http\Requests\Swap\StoreSwapRequest;
use App\Http\Requests\Swap\StoreSwapRequestDetails;
use App\Http\Requests\Swap\StoreSwapRequestDetailsRequest;
use App\Http\Requests\Swap\UpdateSwapDetailsRequest;
use App\Http\Requests\Swap\UpdateSwapRequest;
use App\Http\Resources\SwapResource;
use App\Jobs\SwapJob;
use App\Models\Billing;
use App\Models\Message;
use App\Models\Swap;
use App\Models\SwapExchangeDetails;
use App\Models\SwapRequestDetails;
use App\Models\User;
use App\Services\SwapMessageService;
use App\Services\SwapNotificationService;
use App\Services\SwapRequestService;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwapController extends Controller
{
    const PER_PAGE   = 10;
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

        $swaps->where(function ($query) {
            $userId = auth()->id();
            $query->where('requested_user_id', $userId)
                ->orWhere('exchanged_user_id', $userId);
        }
        );

        if ($request->has('search')) {
            $searchTerm = '%' . $request->search . '%';
            $swaps->where(function ($query) use ($searchTerm) {
                $query->whereHas('user', function ($query) use ($searchTerm) {
                    $query->where('first_name', 'like', $searchTerm)
                        ->orWhere('last_name', 'like', $searchTerm);
                }
                )
                    ->orWhereHas('exchangeDetails', function ($query) use ($searchTerm) {
                        $query->whereHas('product', function ($query) use ($searchTerm) {
                            $query->where('name', 'like', $searchTerm)
                                ->orWhere('description', 'like', $searchTerm);
                        }
                        );
                    }
                    )
                    ->orWhereHas('requestDetail', function ($query) use ($searchTerm) {
                        $query->whereHas('product', function ($query) use ($searchTerm) {
                            $query->where('name', 'like', $searchTerm)
                                ->orWhere('description', 'like', $searchTerm);
                        }
                        );
                    }
                    )
                    ->orWhereHas('initiateDetails', function ($query) use ($searchTerm) {
                        $query->whereHas('product', function ($query) use ($searchTerm) {
                            $query->where('name', 'like', $searchTerm)
                                ->orWhere('description', 'like', $searchTerm);
                        }
                        );
                    }
                    );
            }
            );
        }

        if ($request->sort) {
            $swaps->orderBy('created_at', $request->sort ?? 'desc');
        }

        $swaps = $swaps->with(
            'initiateDetails',
            'exchangeDetails',
            'exchangeDetails.product.productVariations.size',
            'exchangeDetails.product.productVariations.color',
            'user'
        );


        if ($request->get('get_all')) {
            return response()->json(['success' => true, 'data' => SwapResource::collection($swaps->get())->resource]);
        }


        $swap = $swaps->paginate($request->pagination ?? self::PER_PAGE);

        return response()->json(['success' => true, 'data' => SwapResource::collection($swap)->resource]);
    }

//    /**
//     * Create Swap.
//     *
//     * @OA\Post (
//     *     path="/api/swap",
//     *     tags={"Swaps"},
//     *     security={{ "apiAuth": {} }},
//     *     summary="Create a new swap",
//     *
//     *     @OA\Parameter(
//     *         in="query",
//     *         name="requested_user_id",
//     *         required=true,
//     *         description="Requested User ID",
//     *         @OA\Schema(type="integer", example=2),
//     *     ),
//     *     @OA\Parameter(
//     *         in="query",
//     *         name="exchanged_user_id",
//     *         required=true,
//     *         description="Exchanged User ID",
//     *         @OA\Schema(type="integer", example=3),
//     *     ),
//     *     @OA\Parameter(
//     *         in="query",
//     *         name="status",
//     *         required=true,
//     *         description="Status",
//     *         @OA\Schema(type="string", example="pending"),
//     *     ),
//     *     @OA\Parameter(
//     *         in="query",
//     *         name="define_type",
//     *         required=true,
//     *         description="Exchanged Wholesale Amount",
//     *         @OA\Schema(type="enum", example="exchange_product | request_product"),
//     *     ),
//     *     @OA\Parameter(
//     *         in="query",
//     *         name="exchange_product[0][product_id]",
//     *         required=true,
//     *         description="Product ID",
//     *         @OA\Schema(type="number", format="integer", example=1),
//     *     ),
//     *     @OA\Parameter(
//     *         in="query",
//     *         name="exchange_product[0][variation_id]",
//     *         required=true,
//     *         description="Product ID",
//     *         @OA\Schema(type="number", format="integer", example=1),
//     *     ),
//     *     @OA\Parameter(
//     *          in="query",
//     *          name="exchange_product[0][variation_size_id]",
//     *          required=true,
//     *          description="Product ID",
//     *          @OA\Schema(type="number", format="integer", example=1),
//     *      ),
//     *     @OA\Parameter(
//     *          in="query",
//     *          name="exchange_product[0][variation_color_id]",
//     *          required=true,
//     *          description="Product ID",
//     *          @OA\Schema(type="number", format="integer", example=1),
//     *      ),
//     *     @OA\Parameter(
//     *          in="query",
//     *          name="exchange_product[0][variation_quantity]",
//     *          required=true,
//     *          description="Product ID",
//     *          @OA\Schema(type="number", format="integer", example=1),
//     *      ),
//     *     @OA\Response(
//     *          response=200,
//     *          description="success",
//     *
//     *          @OA\JsonContent(
//     *
//     *              @OA\Property(property="success", type="boolean", example="true"),
//     *               @OA\Property(property="errors", type="json", example={"message": {"Swap created successfully."}}),
//     *          ),
//     *      ),
//     *
//     *      @OA\Response(
//     *          response=422,
//     *          description="Invalid data",
//     *
//     *          @OA\JsonContent(
//     *
//     *              @OA\Property(property="success", type="boolean", example="false"),
//     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
//     *          )
//     *      )
//     * )
//     */
    public function store(StoreSwapRequest                $swapRequest,
                          StoreSwapExchangeDetailsRequest $SwapExchangeDetailsRequest): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $swap = Swap::create(
                [
                    'user_id'           => auth()->id(),
                    'requested_user_id' => auth()->id(), // User who requested the swap
                    'exchanged_user_id' => $swapRequest->exchanged_user_id, // User who accepted the swap
                    'status'            => $swapRequest->status,
                ]
            );
            dd("ok");
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
                    'message'         => 'You have a new swap request ' . $swap->id,
                    'receiver_id'     => $swapRequest->exchanged_user_id,
                    'swap_id'         => $swap->id,
                    'sender_id'       => auth()->id(),
                    'conversation_id' => $conversation->id,
                    'message_type'    => 'notification',
                ]
            );

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
     *             @OA\Property(property="data", type="json", example={"uid": "swp-547689"}),
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
    public function show($id)
    {
        $swap = Swap::with('exchangeDetails', 'requestDetail', 'initiateDetails')->where('uid', $id)
            ->orWhere('id', $id)
            ->first();

        if ($swap->requested_user_id == auth()->id() || $swap->exchanged_user_id == auth()->id()) {

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
                           UpdateSwapDetailsRequest $swapExchangeDetailsRequest,
                                                    $id): \Illuminate\Http\JsonResponse
    {

        $swap = Swap::where('uid', $id)
            ->orWhere('id', $id)
            ->first();

        if (!$swap) {
            return response()->json(['success' => false, 'message' => 'Swap not found'], 404);
        }

        try {
            DB::beginTransaction();

            if (($swap->requested_user_id == auth()->id() || $swap->exchanged_user_id == auth()->id()) && $swap->exchanged_user_status == 'approved') {

                $defineType = $swapExchangeDetailsRequest->define_type;

                if (is_null($swapExchangeDetailsRequest->$defineType)) {
                    return response()->json(['success' => false, 'message' => str_replace('_', ' ', $defineType) . ' is empty']);
                }

                $prepareData = SwapRequestService::prepareDetailsData(
                    $swapExchangeDetailsRequest,
                    $swap,
                    $swapExchangeDetailsRequest->define_type
                );

                $this->swapDetailsClassMapper($swapExchangeDetailsRequest->define_type)::insert($prepareData['insertData']);

                if ($updateSwapRequest->deleted_details_id) {
                    SwapRequestService::deleteDetailsData(
                        $updateSwapRequest->deleted_details_id,
                        $swap,
                        SwapRequestService::matchClass($swapExchangeDetailsRequest->define_type)
                    );
                }

                $defineWholeSaleColumn = SwapRequestService::swapColumnMapper($swapExchangeDetailsRequest->define_type)[0];
                $defineTotalCommissionColumn = SwapRequestService::swapColumnMapper($swapExchangeDetailsRequest->define_type)[1];


                $swap->update(
                    [
                        $defineWholeSaleColumn =>
                            (int)$prepareData['wholeSaleAmount'] +
                            (int)$swap->$defineWholeSaleColumn,

                        $defineTotalCommissionColumn =>
                            $prepareData['totalCommission'] +
                            $swap->$defineTotalCommissionColumn,
                    ]
                );

                DB::commit();

                return response()->json(['success' => true, 'data' => $swap], 201);
            }

            return response()->json(['success' => false, 'message' => 'You are not authorized to update this swap'], 401);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update swap', 'errors' => $e->getMessage()], 500);
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
    public function swapApprove($uid): \Illuminate\Http\JsonResponse
    {
        $swap = Swap::where(function ($query) use ($uid) {
            $query->where('exchanged_user_id', auth()->id())
                ->orWhere('requested_user_id', auth()->id());
        }
        )->where('uid', $uid)->first();

        if (!$swap) {
            return response()->json(['success' => false, 'message' => 'Swap not found'], 404);
        }

        if ($swap->exchanged_user_id != auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to approve this swap'], 401);
        }


        if ($swap->exchanged_user_status == 'accepted') {

            $swap->update([
                    'exchanged_user_status' => 'approved',
                    'requested_user_status' => 'approved'
                ]
            );

            NotificationFacade::prepareData(
                $swap,
                [$swap->requested_user_id],
                'Your swap request has been approved'
            )->sendNotification();

            return response()->json(['success' => true, 'message' => 'You approved the swap request'], 200);
        }

        return response()->json(['success' => false, 'message' => 'You are not allow to change the swap status'], 403);
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
    public function swapDecline($uid): \Illuminate\Http\JsonResponse
    {
        $swap = Swap::where(function ($query) use ($uid) {
            $query->where('exchanged_user_id', auth()->id())
                ->orWhere('requested_user_id', auth()->id());
        }
        )->where('uid', $uid)->first();

        if (!$swap) {
            return response()->json(['success' => false, 'message' => 'Swap not found'], 404);
        }

        if ($swap->exchanged_user_id != auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to decline this swap'], 401);
        }

        if ($swap->exchanged_user_status == 'pending') {

            $swap->update([
                    'exchanged_user_status' => 'decline',
                    'requested_user_status' => 'rejected'
                ]
            );

            NotificationFacade::prepareData(
                $swap,
                [$swap->requested_user_id],
                'Your swap request has been declined'
            )->sendNotification();

            return response()->json(['success' => true, 'message' => 'You decline the swap request'], 200);
        }

        return response()->json(['success' => false, 'message' => 'You are not allow to change the swap status'], 403);
    }

    public function swapComplete($uid): \Illuminate\Http\JsonResponse
    {
        $swap = Swap::where(function ($query) use ($uid) {
            $query->where('exchanged_user_id', auth()->id())
                ->orWhere('requested_user_id', auth()->id());
        }
        )->where('uid', $uid)->first();

        if (!$swap) {
            return response()->json(['success' => false, 'message' => 'Swap not found'], 404);
        }

        $user = auth()->user()->load('activePaymentMethod');
        if (!$user->activePaymentMethod) {
            return response()->json(['success' => true, 'message' => 'You are not allow to change the swap status'], 200);
        }

        if ($swap->requested_total_commission < 0.50 || $swap->exchanged_total_commission < 0.50) {
            return response()->json(['success' => false, 'message' => 'Minimum commission is 0.50'], 401);
        }

        if ($swap->requested_user_id !== auth()->id() && $swap->exchanged_user_id !== auth()->id()) {
            return response()->json(['success' => true, 'message' => 'You are not allow to change the swap status'], 200);
        }

        return $this->processSwapCompletion($swap, $user);
    }

    protected function processSwapCompletion($swap, $user)
    {
        $approvalField =
            $swap->requested_user_id === auth()->id() ?
                'requested_user_status' :
                'exchanged_user_status';

        $swap->update([$approvalField => 'completed']);

        $this->sendSwapNotification($swap);

        if ($user->is_super_swaper == 0) {
            $this->handlePayment($swap, $user);
        }

        return response()->json(['success' => true, 'message' => 'You completed the swap request'], 200);
    }

    protected function sendSwapNotification($swap)
    {
        $notificationRecipients =
            $swap->requested_user_id === auth()->id() ?
                [$swap->exchanged_user_id] :
                [$swap->requested_user_id];

        NotificationFacade::prepareData(
            $swap,
            $notificationRecipients,
            'Swap request has been completed'
        )->sendNotification();
    }

    protected function handlePayment($swap, $user)
    {
        $commission =
            $swap->requested_user_id === auth()->id() ?
                $swap->requested_total_commission :
                $swap->exchanged_total_commission;

        $invoiceItem = StripePaymentFacade::createInvoiceItem(
            $user,
            $commission
        );

        Billing::create([
                'user_id'                  => auth()->id(),
                'swap_id'                  => $swap->id,
                'payment_type'             => 'one_time',
                'payment_method_id'        => $user->activePaymentMethod->stripe_payment_method_id,
                'stripe_payment_intent_id' => $invoiceItem->payment_intent,
                'amount'                   => $commission,
            ]
        );
    }

    protected function swapDetailsClassMapper($defineType)
    {
        return $defineType === 'exchange_product' ?
            SwapExchangeDetails::class :
            SwapRequestDetails::class;
    }

}
