<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            ['contact_id' => 'CONTACT-2025-AUG-0001', 'client_id' => 'CLIENT-2025-AUG-0001', 'contact_type' => 'Application', 'contact_number' => '0912-345-6789'],
            ['contact_id' => 'CONTACT-2025-AUG-0002', 'client_id' => 'CLIENT-2025-AUG-0003', 'contact_type' => 'Application', 'contact_number' => '0907-632-3656'],
            ['contact_id' => 'CONTACT-2025-AUG-0003', 'client_id' => 'CLIENT-2025-AUG-0007', 'contact_type' => 'Application', 'contact_number' => '0993-959-7683'],
        ];

        DB::table('contacts')->insert($contacts);
    }
}
