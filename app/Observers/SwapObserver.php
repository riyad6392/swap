<?php

namespace App\Observers;

use App\Models\Swap;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SwapObserver
{
    /**
     * Handle the Swap "created" event.
     */
    public function created(Swap $swap): void
    {
        User::find(Auth::user()->id)
            ->notifications()
            ->create([
                'id' => uniqid(),
                'type' => 'App\Notifications\SwapRequestNotification',
                'notififor_id'=> $swap->requested_user_id,
                'data' => 'Swap request has been sent successfully',
                'notififor_id' => $swap->exchanged_user_id,
            ]);

//        return $notification;
    }

    /**
     * Handle the Swap "updated" event.
     */
    public function updated(Swap $swap): void
    {
        //
    }

    /**
     * Handle the Swap "deleted" event.
     */
    public function deleted(Swap $swap): void
    {
        //
    }

    /**
     * Handle the Swap "restored" event.
     */
    public function restored(Swap $swap): void
    {
        //
    }

    /**
     * Handle the Swap "force deleted" event.
     */
    public function forceDeleted(Swap $swap): void
    {
        //
    }
}
