<?php

namespace App\Services;

use App\Models\ProductVariation;
use App\Models\SwapExchangeDetail;

class SwapRequestService
{
    const COMMISSION_PERCENTAGE = 0.25;

    public static function prepareDetailsData($request, object $swap, string $prepareFor): array
    {
        $insertData = [];
        $wholeSaleAmount = 0;
        $totalCommission = 0;
        foreach ($request->$prepareFor as $product) {
            $variation = ProductVariation::where('id', $product['variation_id'])
                ->where('product_id', $product['product_id'])
                ->first();

            if ($variation) {
                $insertData[] = [
                    'uid' => uniqid(),
                    'swap_id' => $swap->id,
                    'user_id' => auth()->id(),
                    'product_id' => $product['product_id'],
                    'product_variation_id' => $product['variation_id'],
                    'quantity' => $product['variation_quantity'],
                    'unit_price' => $variation->unit_price ?? 0,
                    'amount' => $product['variation_quantity'] *
                        $variation->unit_price ?? 0,
                    'commission' => ($product['variation_quantity'] *
                        $variation->unit_price ?? 0) *
                        self::COMMISSION_PERCENTAGE,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ];
                $wholeSaleAmount += $product['variation_quantity'] * $variation->unit_price ?? 0;
                $totalCommission += ($product['variation_quantity'] * $variation->unit_price ?? 0) * self::COMMISSION_PERCENTAGE;
            }
        }
        return ['insertData' => $insertData, 'wholeSaleAmount' => $wholeSaleAmount, 'totalCommission' => $totalCommission];
    }

    protected function deleteDetailsData($deleted_id, $swap): void
    {
        SwapExchangeDetail::where('user_id', auth()->id())
            ->where('swap_id', $swap->id)
            ->whereIn('id', $deleted_id)
            ->delete();
    }

    protected function calculateTotalAmountAndCommission($swap): array
    {
        $wholeSaleAmount = 0;
        $totalCommission = 0;

        $detailsData = $swap->load('exchangeDetails');

        foreach ($detailsData as $detail) {
            $wholeSaleAmount += $detail->amount;
            $totalCommission += $detail->commission;
        }
        return ['wholeSaleAmount' => $wholeSaleAmount, 'totalCommission' => $totalCommission];
    }

}
