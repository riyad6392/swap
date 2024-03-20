<?php
namespace App\Services;

use Stripe\StripeClient;

class StripePaymentService
{
    private static StripeClient $stripe;

    public static function initialize(): void
    {
        self::$stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
    }

    public function charge($amount, $token)
    {
        // Charge the user's card
    }

    public static function createCustomer($data): \Stripe\Customer
    {
        self::initialize();
        return self::$stripe->customers->create([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    public static function getCustomer($limit = 3): \Stripe\Collection
    {
        self::initialize();
        return self::$stripe->customers->all(['limit' => $limit]);
    }

    public static function deleteCustomer($data): \Stripe\Customer
    {
        self::initialize();
        return self::$stripe->customers->delete($data['stripe_customer_id']);
    }

    public static function createPrice($data): \Stripe\Price
    {
        self::initialize();
//        dd($data['amount']);
        return self::$stripe->prices->create([
            'unit_amount' => $data['amount'],
            'currency' => $data['currency'],
            'recurring' => [
                'interval' => $data['interval'],
                'interval_count' => $data['interval_duration']
            ],
            'product_data' => ['name' => $data['name']],
        ]);
    }

    public function testing(): string
    {
        dd('testing');
        return 'testing';

    }
}
