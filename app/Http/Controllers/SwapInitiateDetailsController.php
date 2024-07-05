<?php

namespace App\Http\Controllers;

use App\Facades\MessageFacade;
use App\Facades\NotificationFacade;
use App\Http\Requests\SwapInitiate\StoreSwapInitiateRequest;
use App\Mail\UserApprovel;
use App\Jobs\SwapJob;
use App\Models\Message;
use App\Models\Swap;
use App\Models\User;
use App\Models\SwapInitiateDetails;
use App\Services\SwapMessageService;
use App\Services\SwapNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\SwapInitiated;
use App\Mail\SwapHighValue;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Mail;

class SwapInitiateDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Create Swap.
     *
     * @OA\Post (
     *     path="/api/swap-initiate",
     *     tags={"Swaps Initiate"},
     *     security={{ "apiAuth": {} }},
     *     summary="Create a new swap",
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="exchanged_user_id",
     *         required=true,
     *         description="Exchanged User ID",
     *         @OA\Schema(type="integer", example=3),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="products[0][product_id]",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example="products[0][1]"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="products[0][product_variation_id]",
     *         required=true,
     *         description="Product Variation ID",
     *         @OA\Schema(type="number", format="integer", example="products[0][product_variation_id]"),
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="products[0][quantity]",
     *          required=true,
     *          description="Product quantity",
     *          @OA\Schema(type="number", format="integer", example="products[0][quantity]"),
     *      ),
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
    public function store(StoreSwapInitiateRequest $swapInitiateRequest)
    {
        try {
            DB::beginTransaction();
            $swap = Swap::create([
                'user_id' => auth()->id(),
                'exchanged_user_id' => $swapInitiateRequest->exchanged_user_id,
                'requested_user_id' => auth()->id(),
                'requested_user_status' => 'requested',
                'exchanged_user_status' => 'pending',
            ]);

            $insertData = [];

            foreach ($swapInitiateRequest->products as $products) {
                $insertData[] = [
                    'swap_id' => $swap->id,
                    'uid' => 'sid-' . uniqid(),
                    'user_id' => auth()->id(),
                    'product_id' => $products['product_id'],
//                    'product_variation_id' => $products['product_variation_id'],
//                    'quantity' => $products['quantity'],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            SwapInitiateDetails::insert($insertData);

            $exchangedUser = User::findOrFail($swapInitiateRequest->exchanged_user_id);
            $requestUser = User::findOrFail(auth()->id());

            MessageFacade::prepareData(
                auth()->id(),
                $swap->exchanged_user_id,
                'private',
                'notification',
                'You have a new swap request ' . $swap->uid,
                [],
                $swap
            )->messageGenerate()->doConversationBroadcast()->doMessageBroadcast();

            NotificationFacade::prepareData(
                $swap,
                [$swap->exchanged_user_id],
                'You have a new swap request ' . $swap->uid,
            )
            ->sendNotification();


            $data = [
                'exchanged_user_first_name' => $exchangedUser->first_name,
                'exchanged_user_last_name' => $exchangedUser->last_name,
                'requested_user_first_name' => $requestUser->first_name,
                'requested_user_last_name' => $requestUser->last_name,
                'to' => 'riyadstudent80@gmail.com',
            ];

            $super_admin = 'riyadstudent80@gmail.com';
            Mail::to($super_admin)->send((new SwapHighValue($data))->afterCommit());


            DB::commit();
            return response()->json(['success' => true, 'message' => 'Swap initiated successfully'], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show($uid)
    {
        $swap = Swap::where(function ($query) use ($uid) {
            $query->where('exchanged_user_id', auth()->id())
                ->orWhere('requested_user_id', auth()->id());
        })->where('uid', $uid)->first();


        if (!$swap) {
            return response()->json(['success' => false, 'message' => 'Swap not found'], 404);
        }

        $swap = $swap->load('user', 'initiateDetails.product.image', 'requestDetail.product.image', 'exchangeDetails.product.image');

        return response()->json(['success' => true, 'data' => $swap], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SwapInitiateDetails $swapInitiateDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SwapInitiateDetails $swapInitiateDetails)
    {
        //
    }

    /**
     * Accept Swap.
     *
     * @OA\Delete  (
     *     path="/api/swap/destroy/{id}",
     *     tags={"Swaps Initiate"},
     *     security={{ "apiAuth": {} }},
     *     summary="Swap Request accepted by message request",
     *     @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Request accepted successfully."}}),
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
    public function destroy($uid)
    {
        $swap = Swap::where(function ($query) use ($uid) {
            $query->where('exchanged_user_id', auth()->id())
                ->orWhere('requested_user_id', auth()->id());
        })->where('uid', $uid)->first();

        if (!$swap) {
            return response()->json(['success' => false, 'message' => 'Swap not found'], 404);
        }

        if ($swap->requested_user_id != auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to delete this swap'], 401);
        }

        if ($swap->exchanged_user_status == 'pending' && $swap->requested_user_status == 'requested') {
            $swap->initiateDetails()->delete();
            $swap->requestDetail()->delete();
            $swap->exchangeDetails()->delete();
            $swap->delete();

            return response()->json(['success' => true, 'message' => 'Swap deleted successfully'], 200);
        }

        return response()->json(['success' => false, 'message' => 'Your Swap request has different status'], 400);

    }

    /**
     * Accept Swap.
     *
     * @OA\Get  (
     *     path="/api/swap-accept/{id}",
     *     tags={"Swaps Initiate"},
     *     security={{ "apiAuth": {} }},
     *     summary="Swp Request accepted by message request",
     *     @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Request accepted successfully."}}),
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

    public function swapAccept($uid)
    {
        $swap = Swap::where(function ($query) use ($uid) {
            $query->where('exchanged_user_id', auth()->id())
                ->orWhere('requested_user_id', auth()->id());
        })->where('uid', $uid)->first();

        if (!$swap) {
            return response()->json(['success' => false, 'message' => 'Swap not found'], 404);
        }

        if ($swap->exchanged_user_id != auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to Accept this swap'], 401);
        }

        if ($swap->exchanged_user_status == 'pending') {

            $swap->update([
                'exchanged_user_status' => 'accepted',
                'requested_user_status' => 'accepted',
            ]);

            return response()->json(['success' => true, 'message' => 'Request accepted successfully'], 200);
        }

        return response()->json(['success' => false, 'message' => 'Your Swap request has different status'], 400);
    }
}
