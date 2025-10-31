<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SponsorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sponsors = [
            ['sponsor_id' => 'SPONSOR-2025-01', 'member_id' => 'MEMBER-2025-AUG-0002', 'sponsor_type' => 'Business Owner', 'designation' => 'Chief Executive Officer', 'organization_name' => 'Ivana Skin'],
            ['sponsor_id' => 'SPONSOR-2025-02', 'member_id' => 'MEMBER-2025-AUG-0003', 'sponsor_type' => 'Other', 'designation' => 'Content Creator', 'organization_name' => 'YouTube'],
            ['sponsor_id' => 'SPONSOR-2025-03', 'member_id' => 'MEMBER-2025-AUG-0004', 'sponsor_type' => 'Business Owner', 'designation' => 'Founder', 'organization_name' => 'Laravel Company'],
            ['sponsor_id' => 'SPONSOR-2025-04', 'member_id' => 'MEMBER-2025-AUG-0005', 'sponsor_type' => 'Politician', 'designation' => 'Senator', 'organization_name' => null],
        ];

        DB::table('sponsors')->insert($sponsors);
    }
}
