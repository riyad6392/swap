<?php

namespace App\Http\Controllers;

use App\Facades\StripePaymentFacade;
use App\Http\Requests\PlanSubscription\DeletePlanSubscriptionRequest;
use App\Http\Requests\PlanSubscription\StorePlanSubscriptionRequest;
use App\Models\PaymentMethods;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlanSubscriptionController extends Controller
{
    /**
     * @param StorePlanSubscriptionRequest $planSubscriptionRequest
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * Create a new Subscription.
     *
     *
     * @OA\Post (path="/api/plan-subscription",
     *     tags={"Subscription"},
     *     security={{ "apiAuth": {} }},

     *     @OA\Parameter(
     *     in="query",
     *     name="plan_id",
     *     required=true,
     *     @OA\Schema(type="integer"),
     *     example="100",
     *     ),
     *
     *     @OA\Parameter(
     *      in="query",
     *      name="first_name",
     *      required=true,
     *      @OA\Schema(type="string"),
     *      example="Imtiaz Ur Rehman",
     *      ),
     *
     *      @OA\Parameter(
     *       in="query",
     *       name="last_name",
     *       required=true,
     *       @OA\Schema(type="string"),
     *       example="Khan",
     *       ),
     *
     *       @OA\Parameter(
     *       in="query",
     *       name="business_name",
     *       required=true,
     *       @OA\Schema(type="string"),
     *       example="Business",
     *       ),
     *
     *     @OA\Parameter(
     *        in="query",
     *        name="business_address",
     *        required=true,
     *        @OA\Schema(type="string"),
     *        example="Dhaka, Bangladesh",
     *        ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Category created successfully."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Not found",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"Please add payment method first!"}}),
     *          )
     *      )
     * )
     */
    public function subscribe(StorePlanSubscriptionRequest $planSubscriptionRequest)
    {
        try {
            DB::beginTransaction();

            $paymentMethods = PaymentMethods::where('user_id', auth()->user()->id)
                ->where('status','=', 'active')
                ->first();

            if (!$paymentMethods) {
                return response()->json(['success' => false, 'message' => 'Please add payment method first!'], 422);
            }

            $plan = Plan::find($planSubscriptionRequest->plan_id);

            if ($plan->count() == 0) {
                return response()->json(['success' => false, 'message' => 'Plan does not exist!'], 422);
            }

            $stripeSubscription = StripePaymentFacade::subscription($plan, auth()->user());

            $previousSubscription = Subscription::where('user_id', auth()->user()->id)
                ->where('status', 'active')
                ->first();

            if ($previousSubscription) {
                StripePaymentFacade::cancelSubscription($previousSubscription->stripe_subscription_id);
                $previousSubscription->update(['status' => 'cancelled']);
            }

            Subscription::create([
                'plan_id' => $planSubscriptionRequest->plan_id,
                'user_id' => auth()->user()->id,
                'start_date' => date('Y-m-d'),
                'end_date' => Carbon::now()->addMonth($plan->interval_duration),
                'amount' => $plan->amount,
                'status' => 'active',
                'stripe_subscription_id' => $stripeSubscription->id,
                'payment_method_id' => $paymentMethods->id,
            ]);

            auth()->user()->update([
                'first_name' => $planSubscriptionRequest->first_name,
                'last_name' => $planSubscriptionRequest->last_name,
                'business_name' => $planSubscriptionRequest->business_name,
                'business_address' => $planSubscriptionRequest->business_address,
                'subscription_is_active' => 1,
                'is_super_swapper' => $plan->interval == 'month' ? 1 : 0
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Subscription created successfully!'], 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Cancel the subscription.
     *
     * @OA\Delete (
     *     path="/api/cancel-subscription/{id}",
     *     tags={"Subscription"},
     *     security={{ "apiAuth": {} }},
     *     description="provide the Subscription id to cancel the subscription",
     *
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Subscription deleted successfully")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Plan not found")
     *         ),
     *     )
     * )
     */
    public function cancelSubscription(string $id)
    {
        try {
            DB::beginTransaction();

            $subscription = Subscription::where('id', $id)
                ->where('user_id', auth()->user()->id)
                ->first();

            if (!$subscription) {
                return response()->json(['success' => false, 'message' => 'Subscription not found!'], 422);
            }


            if ($subscription->status == 'cancelled') {
                return response()->json(['success' => false, 'message' => 'Subscription already cancelled!'], 422);
            }

            StripePaymentFacade::cancelSubscription($subscription->stripe_subscription_id);

            $subscription->update(['status' => 'cancelled']);

            auth()->user()->update(['subscription_is_active' => false]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Subscription cancelled successfully!'], 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
