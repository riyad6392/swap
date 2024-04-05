<?php

namespace App\Services;

use App\Models\ProductVariation;
use App\Models\SwapExchangeDetails;
use App\Models\SwapRequestDetails;

class SwapRequestService
{
    const COMMISSION = 0.25;

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

                $productAmount = $product['variation_quantity'] * $variation->unit_price ?? 0;
                $productCommission = $product['variation_quantity'] * self::COMMISSION;

                $insertData[] = [
                    'uid' => uniqid(),
                    'user_id' => auth()->id(),
                    'swap_id' => $swap->id,
                    'product_id' => $product['product_id'],
                    'product_variation_id' => $product['variation_id'],
                    'quantity' => $product['variation_quantity'],
                    'unit_price' => $variation->unit_price ?? 0,
                    'amount' => $productAmount,
                    'commission' => $productCommission,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ];

                $wholeSaleAmount += $productAmount;
                $totalCommission += $productCommission;
            }
        }
        return ['insertData' => $insertData, 'wholeSaleAmount' => $wholeSaleAmount, 'totalCommission' => $totalCommission];
    }

    public static function deleteDetailsData($deleted_id, $swap, $class): void
    {
        if (gettype($deleted_id) == 'string') $deleted_id = json_decode($deleted_id);

        $class::where('user_id', auth()->id())
            ->where('swap_id', $swap->id)
            ->whereIn('id', $deleted_id)
            ->delete();

    }

    public static function calculateTotalAmountAndCommission($swap, $relation): array
    {
        $wholeSaleAmount = 0;
        $totalCommission = 0;

        $detailsData = $swap->$relation; //relation

        foreach ($detailsData as $detail) {
            $wholeSaleAmount += $detail->amount;
            $totalCommission += $detail->commission;
        }

        return ['wholeSaleAmount' => $wholeSaleAmount, 'totalCommission' => $totalCommission];
    }

    public static function matchClass($define_type): string
    {
        return match ($define_type) {
            'exchange_product' => SwapExchangeDetails::class,
            'request_product' => SwapRequestDetails::class,
        };
    }

    public static function matchRelation($define_type): string
    {
        return match ($define_type) {
            'exchange_product' => 'exchangeDetails',
            'request_product' => 'requestDetail',
        };
    }

}
