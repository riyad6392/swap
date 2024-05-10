<?php

namespace App\Http\Controllers;

use App\Facades\StripePaymentFacade;
use App\Http\Requests\paymentMethod\StorePaymentMethodRequest;
use App\Http\Requests\paymentMethod\UpdatePaymentMethodRequest;
use App\Models\PaymentMethods;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentMethods = PaymentMethods::where('user_id', auth()->id())->get();
        return response()->json(['success' => true, 'message' => $paymentMethods], 200);
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
     *         name="is_active",
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
     *                 @OA\Property(property="is_active", type="string", example="active"),
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
    public function store(StorePaymentMethodRequest $paymentMethodRequest): JsonResponse
    {
        try {
            DB::beginTransaction();

            PaymentMethods::where('user_id', auth()->id())
                ->where('is_active', self::STATUS_ACTIVE)
                ->update(['is_active' => self::STATUS_INACTIVE]);

            $paymentMethod = PaymentMethods::create([
                'method_name' => $paymentMethodRequest->method_name,
                'user_id' => auth()->user()->id,
                'master_key' => $paymentMethodRequest->master_key,
                'master_value' => $paymentMethodRequest->master_value,
                'stripe_payment_method_id' => $paymentMethodRequest->stripe_payment_method_id,
                'is_active' => $paymentMethodRequest->is_active ?? 1,
                'card_brand' => $paymentMethodRequest->card_brand,
                'card_display_brand' => $paymentMethodRequest->card_display_brand,
                'card_last_four' => $paymentMethodRequest->card_last_four,
                'card_exp_month' => $paymentMethodRequest->card_exp_month,
                'card_exp_year' => $paymentMethodRequest->card_exp_year,
                'card_country' => $paymentMethodRequest->card_country,
                'card_funding' => $paymentMethodRequest->card_funding,
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
    public function update(UpdatePaymentMethodRequest $updatePaymentMethodRequest): JsonResponse
    {
        $user = auth()->user();
        $paymentId = trim($updatePaymentMethodRequest->stripe_payment_method_id);
        try {

            $paymentMethod = StripePaymentFacade::updateCustomerPaymentMethod(
                $paymentId,
                $user
            );

            PaymentMethods::where('user_id', auth()->id())
                ->update(['is_active' =>
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
    public function destroy($id)
    {
        $payment_method = PaymentMethods::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$payment_method) {
            return response()->json(['success' => false, 'errors' => ['message' => ['Payment method not found']]], 404);
        }

        try {
            $paymentMethod = StripePaymentFacade::detachCustomerPaymentMethod(
                $payment_method->stripe_payment_method_id,
            );
            $payment_method->delete();
            return response()->json(['success' => true, 'message' => $paymentMethod], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => ['message' => [$exception->getMessage()]]], 500);
        }
    }

    /**
     * Default payment method.
     *
     * @OA\get(
     *     path="/api/default-payment-method/{payment_method_id}",
     *     tags={"Payment Methods"},
     *     security={{ "apiAuth": {} }},
     *     summary="Delete a payment method",
     *     description="Change default payment method.",

     *     @OA\Response(
     *         response=200,
     *         description="Payment method change successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Payment method change successfully"),
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
     *         description="Error occurred while change payment method",
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
    public function defaultPaymentMethod($id): JsonResponse
    {
        $payment_method = PaymentMethods::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$payment_method) {
            return response()->json(['success' => false, 'errors' => ['message' => ['Payment method not found']]], 404);
        }

        try {
            $auth = auth()->id();

            PaymentMethods::where('user_id', $auth)
                ->update(['is_active' =>
                    DB::raw("CASE WHEN id =
                    '{$id}' THEN '" . self::STATUS_ACTIVE .
                        "' ELSE '" .
                        self::STATUS_INACTIVE . "' END")]);

            $paymentMethod = $payment_method->update(['is_active' => self::STATUS_ACTIVE]);

            StripePaymentFacade::attachPaymentMethodToCustomer(
                trim($paymentMethod->stripe_payment_method_id),
                auth()->user()
            );
            return response()->json(['success' => true, 'message' => 'Payment method update successfully'], 200);
        }catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => ['message' => [$exception->getMessage()]]], 500);
        }

    }
}
