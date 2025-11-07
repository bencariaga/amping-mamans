<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User\AffiliatePartner;
use App\Models\Operation\Service;

class SyncAffiliatePartnerServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:affiliate-services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all affiliate partners with all services';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Syncing Affiliate Partners with Services...');
        $this->newLine();

        // Get all affiliate partners and services
        $affiliatePartners = AffiliatePartner::all();
        $services = Service::all();

        if ($affiliatePartners->isEmpty()) {
            $this->error('❌ No affiliate partners found in database!');
            $this->info('💡 Please create affiliate partners first.');
            return 1;
        }

        if ($services->isEmpty()) {
            $this->error('❌ No services found in database!');
            $this->info('💡 Please create services first.');
            return 1;
        }

        $this->info("Found {$affiliatePartners->count()} affiliate partners:");
        foreach ($affiliatePartners as $partner) {
            $this->line("  • {$partner->affiliate_partner_name} ({$partner->affiliate_partner_type})");
        }
        $this->newLine();

        $this->info("Found {$services->count()} services:");
        foreach ($services as $service) {
            $this->line("  • {$service->service_type}");
        }
        $this->newLine();

        // Clear existing data
        $this->info('🗑️  Clearing existing affiliate_partner_services data...');
        DB::table('affiliate_partner_services')->truncate();
        
        // Define service mapping based on partner type
        $serviceMapping = [
            'Hospital' => ['Hospital Bill', 'Laboratory Test', 'Diagnostic Test', 'Hemodialysis'],
            'Hospital / Clinic' => ['Hospital Bill', 'Medical Prescription', 'Laboratory Test', 'Diagnostic Test', 'Hemodialysis'],
            'Clinic' => ['Hospital Bill', 'Medical Prescription', 'Laboratory Test', 'Diagnostic Test'],
            'Pharmacy' => ['Medical Prescription'],
            'Pharmacy / Drugstore' => ['Medical Prescription'],
            'Drugstore' => ['Medical Prescription'],
            'Laboratory' => ['Laboratory Test', 'Diagnostic Test'],
            'Blood Bank' => ['Blood Request'],
            'Dialysis Center' => ['Hemodialysis'],
        ];
        
        // Link partners to appropriate services based on type
        $totalLinks = 0;
        $this->newLine();
        
        foreach ($affiliatePartners as $partner) {
            $partnerType = $partner->affiliate_partner_type;
            
            // Get appropriate service types for this partner
            $allowedServiceTypes = $serviceMapping[$partnerType] ?? [];
            
            // If type not mapped, link to all services
            if (empty($allowedServiceTypes)) {
                $this->warn("⚠️  Unknown partner type: '{$partnerType}' - linking to all services");
                $allowedServiceTypes = $services->pluck('service_type')->toArray();
            }
            
            // Filter services by type
            $partnerServices = $services->filter(function ($service) use ($allowedServiceTypes) {
                return in_array($service->service_type, $allowedServiceTypes);
            });
            
            // Create links
            foreach ($partnerServices as $service) {
                DB::table('affiliate_partner_services')->insert([
                    'affiliate_partner_id' => $partner->affiliate_partner_id,
                    'service_id' => $service->service_id,
                ]);
                $totalLinks++;
            }
            
            $this->info("✓ {$partner->affiliate_partner_name} ({$partnerType}) → {$partnerServices->count()} services");
        }
        
        $this->newLine();

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("✅ Successfully created {$totalLinks} links!");
        $this->info("✅ All {$services->count()} services can now filter {$affiliatePartners->count()} affiliate partners.");
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        $this->info('🎉 Done! You can now test the Request Service Assistance form.');

        return 0;
    }
}
