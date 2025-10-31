<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            [
                'message_id' => 'MESSAGE-2025-AUG-0003',
                'staff_id' => 'STAFF-2025-01',
                'contact_id' => 'CONTACT-2025-AUG-0003',
                'message_text' => 'Greetings, Key  Castañeda ! Here are the important details for your AMPING application today.<br><br>For Patient: Key  Castañeda <br>Service Type: Hospital Bill<br>Billed Amount: ₱ 150<br>Assistance Amount: ₱ 15<br>Affiliate Partner: St. Elizabeth Hospital, Inc.<br>Applied At: October 28, 2025<br>Reapply At: January 26, 2026<br><br>Thank you for your visit with us! Come again!',
                'sent_at' => '2025-10-28 20:24:19',
            ],
        ];

        DB::table('messages')->insert($messages);
    }
}
