<?php

namespace App\Http\Controllers;

use App\Facades\StripePaymentFacade;
use App\Http\Requests\paymentMethod\StorePaymentMethodRequest;
use App\Models\PaymentMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
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
     * Store a newly created resource in storage.
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
                $paymentMethod->stripe_payment_method_id,
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
