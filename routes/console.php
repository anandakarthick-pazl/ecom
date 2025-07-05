<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the subscription check command to run daily
Schedule::command('subscriptions:check-expired')
    ->daily()
    ->description('Check and suspend expired company subscriptions');
