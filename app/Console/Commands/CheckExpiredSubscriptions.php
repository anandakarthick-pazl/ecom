<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuperAdmin\Company;
use Illuminate\Support\Facades\Log;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';
    protected $description = 'Check and suspend companies with expired trials or subscriptions';

    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        // Find companies with expired trials or subscriptions
        $expiredCompanies = Company::where('status', 'active')
            ->where(function($query) {
                $query->where(function($subQuery) {
                    // Trial expired and no subscription
                    $subQuery->where('trial_ends_at', '<', now())
                             ->whereNull('subscription_ends_at');
                })->orWhere(function($subQuery) {
                    // Subscription expired
                    $subQuery->where('subscription_ends_at', '<', now());
                });
            })
            ->get();

        $suspendedCount = 0;

        foreach ($expiredCompanies as $company) {
            // Check if there's a recent payment
            $recentPayment = $company->billings()
                ->where('status', 'paid')
                ->where('billing_date', '>', now()->subDays(5))
                ->exists();

            if (!$recentPayment) {
                $company->update(['status' => 'suspended']);
                $suspendedCount++;
                
                $this->line("Suspended: {$company->name} (ID: {$company->id})");
                
                Log::info("Company suspended due to expired subscription", [
                    'company_id' => $company->id,
                    'company_name' => $company->name,
                    'trial_ends_at' => $company->trial_ends_at,
                    'subscription_ends_at' => $company->subscription_ends_at,
                ]);
            }
        }

        $this->info("Suspended {$suspendedCount} companies with expired subscriptions.");

        // Check for companies expiring soon (7 days warning)
        $expiringSoon = Company::where('status', 'active')
            ->where(function($query) {
                $query->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
                      ->orWhereBetween('subscription_ends_at', [now(), now()->addDays(7)]);
            })
            ->get();

        if ($expiringSoon->count() > 0) {
            $this->warn("Warning: {$expiringSoon->count()} companies will expire within 7 days:");
            foreach ($expiringSoon as $company) {
                $expiryDate = $company->subscription_ends_at ?: $company->trial_ends_at;
                $this->line("- {$company->name} expires on {$expiryDate->format('Y-m-d')}");
            }
        }

        return 0;
    }
}
