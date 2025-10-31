<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $messageTemplates = [
            [
                'msg_tmp_id' => 'MSG-TMP-2025-AUG-1',
                'data_id' => 'DATA-2025-AUG-000000033',
                'msg_tmp_title' => 'Text Message Template 1',
                'msg_tmp_text' => 'Greetings, [$application->applicant->client->member->first_name] [$application->applicant->client->member->middle_name] [$application->applicant->client->member->last_name] [$application->applicant->client->member->suffix]! Here are the important details for your AMPING application today.;;For Patient: [$application->patient->client->member->first_name] [$application->patient->client->member->middle_name] [$application->patient->client->member->last_name] [$application->patient->client->member->suffix];Service Type: [$application->service_type];Billed Amount: ₱ [$application->billed_amount];Assistance Amount: ₱ [$application->assistance_amount];Affiliate Partner: [$application->affiliate_partner->affiliate_partner_name];Applied At: [$application->applied_at];Reapply At: [$application->reapply_at];;Thank you for your visit with us! Come again!',
            ],
        ];

        DB::table('message_templates')->insert($messageTemplates);
    }
}
