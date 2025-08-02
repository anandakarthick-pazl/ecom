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

// Schedule stock notification checks
Schedule::command('stock:check-restocked')
    ->everyFifteenMinutes()
    ->description('Check for restocked products and send notifications');

// Schedule cleanup of old notifications (weekly)
Schedule::call(function () {
    $service = app(\App\Services\EnhancedStockNotificationService::class);
    $service->cleanupOldNotifications(30);
})->weekly()->description('Clean up old stock notifications');
