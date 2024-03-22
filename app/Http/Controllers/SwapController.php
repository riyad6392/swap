<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSwapRequest;
use App\Http\Requests\UpdateSwapRequest;
use App\Models\Swap;
use Illuminate\Support\Facades\DB;

class SwapController extends Controller
{
    const PER_PAGE = 10;


    public function index()
    {
        return Swap::paginate(self::PER_PAGE);
    }


    public function store(StoreSwapRequest $swapRequest)
    {
        try {
            DB::beginTransaction();

            $swap = Swap::create($swapRequest->only(
                [
                    'requested_user_id',
                    'exchanged_user_id',
                    'status',
                    'requested_wholesale_amount',
                    'exchanged_wholesale_amount',
                    'requested_total_commission',
                    'exchanged_total_commission',
                ]
            ));

            DB::commit();
            return response()->json(['success' => true, 'data' => $swap], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function show(Swap $swap)
    {
        return $swap;
    }


    public function update(UpdateSwapRequest $updateSwapRequest, Swap $swap)
    {
        try {
            DB::beginTransaction();
            $swap->update($updateSwapRequest->only(
                [
                    'requested_user_id',
                    'exchanged_user_id',
                    'status',
                    'requested_wholesale_amount',
                    'exchanged_wholesale_amount',
                    'requested_total_commission',
                    'exchanged_total_commission',
                ]
            ));

            DB::commit();
            return response()->json(['success' => true, 'data' => $swap], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update swap'], 500);
        }
    }

    
    public function destroy(Swap $swap)
    {
        $swap->delete();
        return response()->json(['success' => true, 'message' => 'Swap and related data deleted successfully'], 200);
    }
}
