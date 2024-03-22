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

    public function createPaymentMethod($data)
    {
        return $this->stripe->paymentMethods->create([
            'type' => $data['type'],
            'billing_details' => [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => [
                    'country' => $data['country'],
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'postal_code' => $data['postal_code'],
                    'line1' => $data['line1'],
                ],
            ],
            'card' => [
                'number' => $data['number'],
                'exp_month' => $data['exp_month'],
                'exp_year' => $data['exp_year'],
                'cvc' => $data['cvc'],
            ],
        ]);
    }

    public function attachPaymentMethodToCustomer($paymentMethod, $user): \Stripe\PaymentMethod
    {
          return $this->stripe->paymentMethods->attach(
            $paymentMethod,
            ['customer' => $user->stripe_customer_id]
        );
    }

    public function updatePaymentMethod($request, $paymentMethod): \Stripe\PaymentMethod
    {
        return $this->stripe->paymentMethods->update($paymentMethod, [
            'billing_details' => [
                'name' => $request->billing_details['name'] ?? null,
                'email' => $request->billing_details['email'] ?? null,
                'phone' => $request->billing_details['phone'] ?? null,
                'address' => [
                    'country' => $request->billing_details['address']['country_code'],
                    'city' => $request->billing_details['address']['city'],
                    'state' => $request->billing_details['address']['state'],
                    'postal_code' => $request->billing_details['address']['postal_code'],
                    'line1' => $request->billing_details['address']['line1'],
                ],
            ],
            'card' => [
                'exp_month' => $request->card['exp_month'],
                'exp_year' => $request->card['exp_year'],
            ],
        ]);
    }
}
