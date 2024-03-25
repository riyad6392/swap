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
    public function subscribe(StorePlanSubscriptionRequest $planSubscriptionRequest)
    {
        try {
            DB::beginTransaction();

            $paymentMethods = PaymentMethods::where('user_id', auth()->user()->id)
                ->where('status', 'active')
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

            auth()->user()->update(['subscription_is_active' => true]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Subscription created successfully!'], 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function cancelSubscription(DeletePlanSubscriptionRequest $deletePlanSubscriptionRequest)
    {
        dd($deletePlanSubscriptionRequest->subscription_id);
        try {
            DB::beginTransaction();

            $subscription = Subscription::where('id', $deletePlanSubscriptionRequest->subscription_id)
                ->where('user_id', auth()->user()->id)
                ->firstOrFail();

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
