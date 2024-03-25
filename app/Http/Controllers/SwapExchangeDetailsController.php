<?php

namespace App\Http\Controllers;

use App\Http\Requests\Swap\StoreSwapExchageDetails;
use App\Http\Requests\Swap\UpdateSwapExchangeDetails;
use App\Models\SwapExchangeDetail;
use Illuminate\Http\Request;

class SwapExchangeDetailsController extends Controller
{
    const PER_PAGE = 10;
    public function index()
    {
        $swap_exchange_details = SwapExchangeDetail::paginate(self::PER_PAGE);
        return response()->json(['success' => true, 'data' => $swap_exchange_details]);
    }

    public function store(StoreSwapExchageDetails $storeSwapExchangeDetails)
    {
        try {
            DB::beginTransaction();

            $swap = SwapExchangeDetail::create($storeSwapExchangeDetails->only(
                [
                    'swap_id',
                    'user_id',
                    'product_id',
                    'product_variation_id',
                    'quantity',
                    'unit_price',
                    'amount',
                    'commission',
                ]
            ));

            DB::commit();
            return response()->json(['success' => true, 'data' => $swap], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SwapExchangeDetail $SwapExchangeDetail)
    {
        try {
            return response()->json(['success' => true, 'data' => $SwapExchangeDetail], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve product'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SwapExchangeDetail $SwapExchangeDetail)
    {
        return response()->json(['success' => true, 'data' => $SwapExchangeDetail], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSwapExchangeDetails $updateSwapExchangeDetails, SwapExchangeDetail $SwapExchangeDetail)
    {
        try {
            DB::beginTransaction();
            $updateSwapExchangeDetails->update($updateSwapExchangeDetails->only(
                [
                    'swap_id',
                    'user_id',
                    'product_id',
                    'product_variation_id',
                    'quantity',
                    'unit_price',
                    'amount',
                    'commission',
                ]
            ));

            DB::commit();
            return response()->json(['success' => true, 'data' => $SwapExchangeDetail], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update swap'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SwapExchangeDetail $SwapExchangeDetail)
    {
        $SwapExchangeDetail->delete();
        return response()->json(['success' => true, 'message' => 'Swap exchange details data deleted successfully'], 200);
    }
}
