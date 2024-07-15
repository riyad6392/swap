<?php
namespace App\Services;

use Psy\Util\Str;
use Stripe\Stripe;
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

    public function updateCustomer($data): \Stripe\Customer
    {
        return $this->stripe->customers->update(
            $data['stripe_customer_id'],
            ['name' => $data['name'],
             "email" => $data['email'],
            ]
        );
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

    }

//    public function delete

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

        return $this->updateCustomerPaymentMethod($paymentMethod, $user);
    }
    public function updateCustomerPaymentMethod($paymentMethod, $user): \Stripe\Customer
    {
        return $this->stripe->customers->update(
            $user->stripe_customer_id,
            ['invoice_settings' => ['default_payment_method' => $paymentMethod]]
        );
    }
    public function detachCustomerPaymentMethod($paymentMethod)
    {
        return $this->stripe->paymentMethods->detach($paymentMethod);
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

    public function invoiceList($limit = 3): \Stripe\Collection
    {
        return $this->stripe->invoices->all(['limit' => $limit]);
    }

    public function createInvoiceItem($userData , $commission)
    {
        $paymentIntentData =  $this->stripe->paymentIntents->create([
            'customer' => $userData['stripe_customer_id'],
            'amount' => (double) $commission * 100,
            'currency' => 'USD',
            'description' => 'This is just a test invoice item',
        ]);

        return $this->stripe->paymentIntents->confirm(
            $paymentIntentData->id,
            [
                'payment_method' => $userData->activePaymentMethod->stripe_payment_method_id,
                'return_url' => 'https://example.com/return',
            ]
        );
    }

    public function transactionList($limit = 3): \Stripe\Collection
    {
        return $this->stripe->paymentIntents->all(['limit' => $limit]);
//        return $this->stripe->issuing->transactions->all(['limit' => 3]);

    }
}
