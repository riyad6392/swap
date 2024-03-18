<?php
namespace App\Services;

class StripePaymentService
{
    public function __construct()
    {
        $stripe = new \Stripe\StripeClient('sk_test_51KWED5IZ9zy7k2DvYdcVlu0k8YTlu715bhYKBHqS9FNfJN2OHLzCZJmB3neQWLFVfcFgXUSzqZiIbrWPlrWVOunR00RQf1D07h');
    }
    public function charge($amount, $token)
    {
        // Charge the user's card
    }
}
