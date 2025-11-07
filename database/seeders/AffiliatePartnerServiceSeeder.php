<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User\AffiliatePartner;
use App\Models\Operation\Service;

class AffiliatePartnerServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all affiliate partners and services
        $affiliatePartners = AffiliatePartner::all();
        $services = Service::all();

        if ($affiliatePartners->isEmpty() || $services->isEmpty()) {
            $this->command->warn('No affiliate partners or services found. Please seed them first.');
            return;
        }

        $this->command->info('Found ' . $affiliatePartners->count() . ' affiliate partners and ' . $services->count() . ' services.');

        // Clear existing data
        DB::table('affiliate_partner_services')->truncate();
        $this->command->info('Cleared existing affiliate_partner_services data.');

        $totalLinks = 0;

        // SIMPLE APPROACH: Link ALL affiliate partners to ALL services
        // This ensures every service has filtering enabled
        foreach ($affiliatePartners as $partner) {
            foreach ($services as $service) {
                DB::table('affiliate_partner_services')->insert([
                    'affiliate_partner_id' => $partner->affiliate_partner_id,
                    'service_id' => $service->service_id,
                ]);
                $totalLinks++;
            }
            $this->command->info("✓ Linked {$partner->affiliate_partner_name} to all {$services->count()} services.");
        }

        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->command->info("✅ Successfully created {$totalLinks} affiliate partner-service links!");
        $this->command->info("✅ All {$services->count()} services now have filtering enabled for all {$affiliatePartners->count()} affiliate partners.");
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
    }
}
