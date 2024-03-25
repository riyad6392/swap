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

    public function subscription($plan, $user )
    {
        return $this->stripe->subscriptions->create([
            'customer' => $user->stripe_customer_id,
            'items' => [['price' => $plan->stripe_price_id]],
        ]);
    }

    public function attachPaymentMethodToCustomer($paymentMethod, $user): \Stripe\Customer
    {
        $this->stripe->paymentMethods->attach(
            $paymentMethod,
            ['customer' => $user->stripe_customer_id]
        );
        return $this->stripe->customers->update(
            $user->stripe_customer_id,
            ['invoice_settings' => ['default_payment_method' => $paymentMethod]]
        );
    }
    public function cancelSubscription($subscriptionId): \Stripe\Subscription
    {
        return $this->stripe->subscriptions->cancel(
            $subscriptionId,
            []
        );
    }

    public function resumeSubscription( $subscriptionId): \Stripe\Subscription{
        return $this->stripe->subscriptions->resume(
            $subscriptionId,
            ['billing_cycle_anchor' => 'now']
        );
    }
}
