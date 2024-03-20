<?php
namespace App\Services;

use Stripe\StripeClient;

class StripePaymentService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
    }

    public function charge($amount, $token)
    {
        // Charge the user's card
    }

    public function createCustomer($data): \Stripe\Customer
    {
        return $this->stripe->customers->create([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    public function getCustomer($limit = 3): \Stripe\Collection
    {
        return $this->stripe->customers->all(['limit' => $limit]);
    }

    public function deleteCustomer($data): \Stripe\Customer
    {
        return $this->stripe->customers->delete($data['stripe_customer_id']);
    }

    public function createPrice($data): \Stripe\Plan
    {
        $product = $this->stripe->products->create([
            'name' => $data['name'],
        ]);

        return $this->stripe->plans->create([
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'interval' => $data['interval'],
            'interval_count' => $data['interval_duration'],
            'product' => $product->id,
        ]);
    }

    public function updatePrice($data): \Stripe\Price
    {
        return $this->stripe->prices->update($data['stripe_price_id'], [
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
        return 'testing';

    }
}
