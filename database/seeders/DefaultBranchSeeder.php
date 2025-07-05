<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SuperAdmin\Company;
use App\Models\Branch;

class DefaultBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all companies that don't have any branches
        $companiesWithoutBranches = Company::whereDoesntHave('branches')->get();

        foreach ($companiesWithoutBranches as $company) {
            // Create a default main branch for each company
            Branch::create([
                'company_id' => $company->id,
                'name' => 'Main Branch',
                'code' => 'BR001',
                'email' => $company->email,
                'phone' => $company->phone,
                'address' => $company->address,
                'city' => $company->city,
                'state' => $company->state,
                'country' => $company->country,
                'postal_code' => $company->postal_code,
                'status' => 'active',
                'description' => 'Default main branch for ' . $company->name,
                'manager_name' => null,
                'manager_email' => null,
                'manager_phone' => null,
                'settings' => [
                    'is_main_branch' => true,
                    'created_automatically' => true
                ]
            ]);

            $this->command->info("Created default branch for company: {$company->name}");
        }

        // Update any existing users without branch assignment to main branch
        $companies = Company::with(['branches', 'users'])->get();
        
        foreach ($companies as $company) {
            $mainBranch = $company->branches()->where('code', 'BR001')->first();
            
            if ($mainBranch) {
                // Assign users without branch to main branch
                $usersWithoutBranch = $company->users()->whereNull('branch_id')->get();
                
                foreach ($usersWithoutBranch as $user) {
                    $user->update(['branch_id' => $mainBranch->id]);
                    $this->command->info("Assigned user {$user->name} to main branch of {$company->name}");
                }
            }
        }
    }
}
