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

    public function createPrice($data): \Stripe\Price
    {
        return $this->stripe->prices->create([
            'unit_amount' => $data['amount'],
            'currency' => $data['currency'],
            'recurring' => [
                'interval' => $data['interval'],
                'interval_count' => $data['interval_duration']
            ],
            'product_data' => ['name' => $data['name']],
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

    public function subscription($user, $plan): string
    {
        return $this->stripe->subscriptions->create([
            'customer' => $user->stripe_customer_id,
            'items' => [['price' => $plan->stripe_price_id]],
        ]);
    }

    public function paymentMethod($data): \Stripe\PaymentMethod
    {
        $this->stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number' => $data['number'],
                'exp_month' => $data['exp_month'],
                'exp_year' => $data['exp_year'],
                'cvc' => $data['cvc'],
            ],
        ]);

        return $this->stripe->paymentMethods->attach(
            $paymentMethod,
            ['customer' => $clientId->id]
        );
    }
}
