<?php

namespace App\Http\Controllers;

use App\Facades\StripePaymentFacade;
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

            $plan = Plan::with('planDetails')->find($planSubscriptionRequest->plan_id);

            if ($plan->count() == 0) {
                return response()->json(['success' => false, 'message' => 'Plan does not exist!'], 422);
            }

            $stripeSubscription = StripePaymentFacade::subscription($plan, auth()->user());

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

            return response()->json(['success' => true, 'message' => 'Subscription created successfully!'], 200);

        }catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }

    }
}
