<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SyncMonthlyUsers;
use App\Jobs\SyncRevenuePerMonth;
use App\Jobs\SyncTopProducts;
use App\Jobs\SyncPageViews;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Here you may define all of your Closure based console commands and
| scheduled tasks. Each Closure is bound to a command instance allowing
| a simple approach to interacting with each command's IO methods.
|
*/

// Example default command
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// -----------------------
// Analytics Job Scheduling
// -----------------------

Schedule::job(new SyncMonthlyUsers)->daily();
Schedule::job(new SyncRevenuePerMonth)->daily();
Schedule::job(new SyncTopProducts)->daily();
Schedule::job(new SyncPageViews('home', 1))->hourly();
