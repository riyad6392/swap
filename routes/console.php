<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {

    $this->comment(Inspiring::quote());
    Schedule::call(function () {
        info('Inspiring quote: ' . Inspiring::quote());
//        DB::table('recent_users')->delete();
    })->daily();



})->purpose('Display an inspiring quote');


