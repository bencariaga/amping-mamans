<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceOptimizationService
{
    /**
     * Optimize database queries with eager loading
     */
    public function optimizeEagerLoading()
    {
        // Set default eager loading for models to prevent N+1 issues
        return [
            'Sponsor' => ['member', 'budgetUpdates'],
            'BudgetUpdate' => ['sponsor', 'data'],
            'Client' => ['member', 'occupation', 'contacts'],
            'Household' => ['client.member', 'client.applicant', 'client.patient'],
            'Application' => ['applicant.client.member', 'affiliatePartner', 'expenseRange.service'],
        ];
    }

    /**
     * Cache frequently accessed data
     */
    public function cacheFrequentData()
    {
        // Cache tariff lists for 1 hour
        Cache::remember('tariff_lists_grouped', 3600, function () {
            return DB::table('tariff_lists')
                ->select('data_id', DB::raw('MAX(effectivity_date) as latest_date'))
                ->groupBy('data_id')
                ->orderBy('latest_date', 'desc')
                ->get();
        });

        // Cache services for 24 hours
        Cache::remember('all_services', 86400, function () {
            return DB::table('services')->get();
        });

        // Cache occupations for 24 hours
        Cache::remember('all_occupations', 86400, function () {
            return DB::table('occupations')
                ->orderBy('occupation')
                ->pluck('occupation')
                ->toArray();
        });
    }

    /**
     * Add database indexes for frequently queried columns
     */
    public function suggestIndexes()
    {
        return [
            'members' => ['first_name', 'last_name', 'full_name', 'account_id'],
            'clients' => ['member_id', 'client_id'],
            'applicants' => ['client_id', 'applicant_id'],
            'patients' => ['client_id', 'patient_id', 'applicant_id'],
            'sponsors' => ['member_id', 'sponsor_id'],
            'budget_updates' => ['sponsor_id', 'data_id', 'possessor', 'reason'],
            'tariff_lists' => ['data_id', 'effectivity_date', 'tariff_list_id'],
            'expense_ranges' => ['tariff_list_id', 'service_id'],
            'applications' => ['applicant_id', 'application_id'],
        ];
    }

    /**
     * Optimize query builders to use select specific columns
     */
    public function optimizeQuerySelects()
    {
        return [
            'members_list' => ['member_id', 'first_name', 'middle_name', 'last_name', 'suffix', 'full_name'],
            'clients_list' => ['client_id', 'member_id', 'birthdate', 'civil_status', 'monthly_income'],
            'sponsors_list' => ['sponsor_id', 'member_id', 'sponsor_type', 'designation', 'organization_name'],
        ];
    }

    /**
     * Clear all optimization caches
     */
    public function clearOptimizationCaches()
    {
        Cache::forget('tariff_lists_grouped');
        Cache::forget('all_services');
        Cache::forget('all_occupations');
        
        // Clear Laravel caches
        \Artisan::call('cache:clear');
        \Artisan::call('view:clear');
        \Artisan::call('route:clear');
        \Artisan::call('config:clear');
    }
}
