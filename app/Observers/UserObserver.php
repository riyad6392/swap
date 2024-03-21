<?php

namespace App\Observers;

use App\Facades\StripePaymentFacade;
use App\Models\User;
use App\Services\StripePaymentService;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $customer = StripePaymentFacade::createCustomer($user);
        $user->stripe_customer_id = $customer->id;
        $user->save();
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $customer = StripePaymentFacade::deleteCustomer($user);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
