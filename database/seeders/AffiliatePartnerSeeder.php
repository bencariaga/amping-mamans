<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AffiliatePartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $affiliatePartners = [
            ['ap_id' => 'AP-2025-01', 'account_id' => 'ACCOUNT-2025-AUG-0006', 'ap_name' => 'St. Elizabeth Hospital, Inc.', 'ap_type' => 'Hospital / Clinic'],
            ['ap_id' => 'AP-2025-02', 'account_id' => 'ACCOUNT-2025-AUG-0007', 'ap_name' => 'Rojon Pharmacy', 'ap_type' => 'Pharmacy / Drugstore'],
            ['ap_id' => 'AP-2025-03', 'account_id' => 'ACCOUNT-2025-AUG-0008', 'ap_name' => 'Auguis Clinic and Hospital', 'ap_type' => 'Hospital / Clinic'],
            ['ap_id' => 'AP-2025-04', 'account_id' => 'ACCOUNT-2025-AUG-0009', 'ap_name' => 'Mercury Drug Corporation', 'ap_type' => 'Pharmacy / Drugstore'],
        ];

        DB::table('affiliate_partners')->insert($affiliatePartners);
    }
}
