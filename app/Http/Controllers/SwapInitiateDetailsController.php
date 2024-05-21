<?php

namespace App\Http\Controllers;

use App\Http\Requests\SwapInitiate\StoreSwapInitiateRequest;
use App\Models\Swap;
use App\Models\SwapInitiateDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * *     ),
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
                'request_user_status' => 'requested',
                'exchange_user_status' => 'pending',
            ]);

            $insertData = [];

            foreach ($swapInitiateRequest->products as $products) {
                $insertData[] = [
                    'swap_id' => $swap->id,
                    'uid' => 'sid-'.uniqid(),
                    'user_id' => auth()->id(),
                    'product_id' => $products['product_id'],
//                    'product_variation_id' => $products['product_variation_id'],
//                    'quantity' => $products['quantity'],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ];
            }

            SwapInitiateDetails::insert($insertData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Swap initiated successfully'], 200);

        }catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(SwapInitiateDetails $swapInitiateDetails)
    {
        //
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
     * Remove the specified resource from storage.
     */
    public function destroy(SwapInitiateDetails $swapInitiateDetails)
    {
        //
    }
}
