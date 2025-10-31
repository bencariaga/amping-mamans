<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'role_id' => 'ROLE-2025-1',
                'data_id' => 'DATA-2025-AUG-000000010',
                'role' => 'Program Head',
                'allowed_actions' => 'Create, view, edit, and deactivate accounts of staff members, applicants, sponsors, affiliate partners, and services. Create, view, edit, archive, download, and print reports, including the AMPING\'s financial status and user activity data. Create, view, edit, archive, download, and print templates for assistance request forms, guarantee letters, and text messages. Create, view, edit, and delete tariff lists and change the version of tariff lists to use for assistance amount calculation. Create, view, edit, and delete staff role names and client occupation names. Assign and reassigned roles to staff members. Approve or reject assistance requests and authorize guarantee letters. Send text messages to applicants with approved guarantee letters. Update, add to, and monitor the program budget from government funds, sponsors, and other sources. Delete system cache and log data when necessary',
                'access_scope' => 'Full access to every web page, every feature, and every module, without restrictions. Full access to profiles and system activities of staff members, applicants, patients, sponsors, and affiliate partners. Full access to templates for assistance request forms, guarantee letters, and text messages. Full access to financial records, such as budgets, expenses, and funding sources. Full access to staff role and client occupation names, and tariff lists. Full access to staff role and tariff list adjustments. Full access to data and account archiving, deletion, and deactivation. Full access to logs and reports',
            ],
            [
                'role_id' => 'ROLE-2025-2',
                'data_id' => 'DATA-2025-AUG-000000011',
                'role' => 'Encoder',
                'allowed_actions' => 'Create, view, edit, and deactivate accounts of staff members, applicants, sponsors, affiliate partners, and services. View and use assistance request templates to create assistance request forms. View the AMPING\'s financial status, including the program budget sources from government funds, sponsors, and other sources. View the staff role names and client occupation names. View the roles of staff members. View accounts of staff members, applicants, sponsors, affiliate partners, and services',
                'access_scope' => 'Access limited to viewing and editing account profiles. Access limited to viewing templates for assistance request forms. Access limited to viewing financial records, such as budgets, expenses, and funding sources. Access limited to viewing staff roles, client occupations, and tariff list versions',
            ],
            [
                'role_id' => 'ROLE-2025-3',
                'data_id' => 'DATA-2025-AUG-000000012',
                'role' => 'GL Operator',
                'allowed_actions' => 'Approve or reject assistance requests and authorize guarantee letters. View the AMPING\'s financial status, including the program budget sources from government funds, sponsors, and other sources. View the roles of staff members. View the version of tariff lists to use for assistance amount calculation. View and use guarantee letter templates to create guarantee letters. View accounts of staff members, applicants, sponsors, affiliate partners, and services',
                'access_scope' => 'Access limited to viewing account profiles. Access limited to viewing templates for guarantee letters. Access limited to approving and rejecting assistance requests and authorizing guarantee letters',
            ],
            [
                'role_id' => 'ROLE-2025-4',
                'data_id' => 'DATA-2025-AUG-000000003',
                'role' => 'SMS Operator',
                'allowed_actions' => 'Send text messages to applicants with approved guarantee letters. View and use assistance request templates to create assistance request forms. View the AMPING\'s financial status, including the program budget sources from government funds, sponsors, and other sources. View the roles of staff members. View the version of tariff lists to use for assistance amount calculation. View accounts of staff members, applicants, sponsors, affiliate partners, and services',
                'access_scope' => 'Access limited to viewing account profiles. Access limited to viewing templates for text messages. Access limited to sending text messages to applicants with approved guarantee letters',
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}
