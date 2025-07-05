<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuperAdmin\Company;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TrialExpiryAlert;

class CheckTrialExpiry extends Command
{
    protected $signature = 'check:trial-expiry';
    protected $description = 'Check for trial expiry and send notifications';

    public function handle()
    {
        $this->info('Checking for trial expiry...');

        // Companies expiring in 7 days
        $companiesExpiringSoon = Company::where('trial_ends_at', '<=', now()->addDays(7))
                                       ->where('trial_ends_at', '>', now())
                                       ->where('status', 'active')
                                       ->get();

        // Companies expired today
        $companiesExpiredToday = Company::whereDate('trial_ends_at', today())
                                       ->where('status', 'active')
                                       ->get();

        // Send notifications for companies expiring soon
        foreach ($companiesExpiringSoon as $company) {
            $daysRemaining = now()->diffInDays($company->trial_ends_at, false);
            
            if (in_array($daysRemaining, [7, 3, 1])) { // Send on 7, 3, and 1 day before expiry
                try {
                    // Send to company admin
                    $adminUser = $company->users()->where('role', 'admin')->first();
                    if ($adminUser) {
                        Mail::to($adminUser->email)->send(new TrialExpiryAlert($company, $daysRemaining));
                    }

                    // Send to super admins
                    $superAdmins = User::superAdmins()->get();
                    foreach ($superAdmins as $superAdmin) {
                        Mail::to($superAdmin->email)->send(new TrialExpiryAlert($company, $daysRemaining));
                    }

                    $this->info("Sent trial expiry notification for {$company->name} (expires in {$daysRemaining} days)");
                } catch (\Exception $e) {
                    $this->error("Failed to send notification for {$company->name}: " . $e->getMessage());
                }
            }
        }

        // Handle expired companies
        foreach ($companiesExpiredToday as $company) {
            try {
                // Send expiry notification
                $adminUser = $company->users()->where('role', 'admin')->first();
                if ($adminUser) {
                    Mail::to($adminUser->email)->send(new TrialExpiryAlert($company, 0));
                }

                // Send to super admins
                $superAdmins = User::superAdmins()->get();
                foreach ($superAdmins as $superAdmin) {
                    Mail::to($superAdmin->email)->send(new TrialExpiryAlert($company, 0));
                }

                // Suspend the company if no active subscription
                if (!$company->subscription_ends_at || $company->subscription_ends_at->isPast()) {
                    $company->update(['status' => 'suspended']);
                    $this->info("Suspended {$company->name} due to trial expiry");
                }

                $this->info("Sent trial expired notification for {$company->name}");
            } catch (\Exception $e) {
                $this->error("Failed to handle expired company {$company->name}: " . $e->getMessage());
            }
        }

        $this->info('Trial expiry check completed.');
        $this->info("Companies expiring soon: " . $companiesExpiringSoon->count());
        $this->info("Companies expired today: " . $companiesExpiredToday->count());
    }
}
