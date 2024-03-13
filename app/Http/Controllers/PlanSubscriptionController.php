<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanSubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'price' => 'required|numeric',
            'payment_type' => 'required|in:one-time,monthly,yearly',
            'status' => 'required|in:active,inactive',
            'payment_method' => 'required|in:paypal,stripe',
            'payment_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        Subscription::create([
            'plan_id' => $request->plan_id,
            'user_id' => $request->user_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'price' => $request->price,
            'payment_type' => $request->payment_type,
            'status' => $request->status,
            'payment_method' => $request->payment_method,
            'payment_token' => $request->payment_token,
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
        ]);

        User::update(['subscription_is_active' => true]);

        return response()->json(['success' => true, 'message' => 'Subscription created successfully!'], 200);

    }
}
