<?php

namespace App\Http\Controllers;

use App\Facades\StripePaymentFacade;
use App\Http\Requests\paymentMethod\StorePaymentMethodRequest;
use App\Http\Requests\paymentMethod\UpdatePaymentMethodRequest;
use App\Models\PaymentMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

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
     * Store a newly created payment method.
     *
     * @OA\Post(
     *     path="/api/payment-method",
     *     tags={"Payment Methods"},
     *     security={{ "apiAuth": {} }},
     *
     *     summary="Store a new payment method",
     *     description="Store a newly created payment method.",
     *     operationId="storePaymentMethod",
     *     @OA\Parameter(
     *         in="query",
     *         name="method_name",
     *         required=true,
     *         description="Name of the payment method",
     *         @OA\Schema(type="string", example="Credit Card"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="master_key",
     *         required=true,
     *         description="Master key",
     *         @OA\Schema(type="string", example="123456789"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="master_value",
     *         required=true,
     *         description="Master value",
     *         @OA\Schema(type="string", example="123"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="stripe_payment_method_id",
     *         required=true,
     *         description="Stripe payment method ID",
     *         @OA\Schema(type="string", example="pm_123456789"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="status",
     *         required=false,
     *         description="Status of the payment method",
     *         @OA\Schema(type="string", enum={"active", "inactive"}, default="active"),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment method created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="method_name", type="string", example="Credit Card"),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="master_key", type="string", example="123456789"),
     *                 @OA\Property(property="master_value", type="string", example="123"),
     *                 @OA\Property(property="stripe_payment_method_id", type="string", example="pm_123456789"),
     *                 @OA\Property(property="status", type="string", example="active"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       ),
     *     @OA\Response(
     *         response=500,
     *         description="Error occurred while storing payment method",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="array",
     *                     @OA\Items(type="string", example="Error message goes here")
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function store(StorePaymentMethodRequest $paymentMethodRequest)
    {
        try {
            DB::beginTransaction();

            $paymentMethod = PaymentMethods::create([
                'method_name' => $paymentMethodRequest->method_name,
                'user_id' => auth()->user()->id,
                'master_key' => $paymentMethodRequest->master_key,
                'master_value' => $paymentMethodRequest->master_value,
                'stripe_payment_method_id' => $paymentMethodRequest->stripe_payment_method_id,
                'status' => $paymentMethodRequest->status ?? 'active',
            ]);

            $paymentMethod = StripePaymentFacade::attachPaymentMethodToCustomer(
                trim($paymentMethod->stripe_payment_method_id),
                auth()->user()
            );

            DB::commit();

            return response()->json(['success' => true, 'message' => $paymentMethod], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => ['message' => [$exception->getMessage()]]], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified payment method.
     *
     * @OA\Put(
     *     path="/api/update-payment-method",
     *     tags={"Payment Methods"},
     *     security={{ "apiAuth": {} }},
     *     summary="Update an existing payment method",
     *     description="Update the specified payment method.",
     *     operationId="updatePaymentMethod",
     *     @OA\Parameter(
     *         in="query",
     *         name="stripe_payment_method_id",
     *         required=true,
     *         description="Stripe payment method ID",
     *         @OA\Schema(type="string", example="pm_123456789"),
     *     ),
     *    @OA\Response(
     *         response=200,
     *         description="Payment method updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="errors", type="json", example={"message": "Payment method updated successfully."}),
     *         ),
     *     ),
     *      @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       ),
     *     @OA\Response(
     *         response=500,
     *         description="Error occurred while updating payment method",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="array",
     *                     @OA\Items(type="string", example="Error message goes here")
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function update(UpdatePaymentMethodRequest $updatePaymentMethodRequest)
    {
        $user = auth()->user();
        $paymentId = trim($updatePaymentMethodRequest->stripe_payment_method_id);
        try {

            $paymentMethod = StripePaymentFacade::updateCustomerPaymentMethod(
                $paymentId,
                $user
            );

            PaymentMethods::where('user_id', auth()->id())
                ->update(['status' =>
                    \DB::raw("CASE WHEN stripe_payment_method_id =
                    '{$paymentId}' THEN '" . self::STATUS_ACTIVE .
                        "' ELSE '" .
                        self::STATUS_INACTIVE . "' END")]);

            return response()->json(['success' => true, 'message' => $paymentMethod], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => ['message' => [$exception->getMessage()]]], 500);
        }
    }

    /**
     * Delete the specified payment method.
     *
     * @OA\Delete(
     *     path="/api/delete-payment-method/{payment_method_id}",
     *     tags={"Payment Methods"},
     *     security={{ "apiAuth": {} }},
     *     summary="Delete a payment method",
     *     description="Delete the specified payment method.",
     *     operationId="deletePaymentMethod",
     *     @OA\Parameter(
     *         in="path",
     *         name="payment_method_id",
     *         required=true,
     *         description="Stripe payment method ID",
     *         @OA\Schema(type="integer", example="pm_123456789"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment method deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Payment method deleted successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       ),
     *     @OA\Response(
     *         response=500,
     *         description="Error occurred while deleting payment method",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="message", type="array",
     *                     @OA\Items(type="string", example="Error message goes here")
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function destroy($payment_method_id)
    {
        try {
            $paymentMethod = StripePaymentFacade::detachCustomerPaymentMethod(
                $payment_method_id
            );
            PaymentMethods::where(
                [
                    ['user_id', auth()->id()],
                    ['stripe_payment_method_id', $payment_method_id]
                ])
                ->update(['status', PaymentMethods::STATUS_INACTIVE]);
            return response()->json(['success' => true, 'message' => $paymentMethod], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => ['message' => [$exception->getMessage()]]], 500);
        }
    }
}
