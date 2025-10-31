<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Disable foreign key checks temporarily to allow mass updates across related tables.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Reorganize IDs for all tables with custom IDs
        $this->reorganizeTableIds('accounts', 'account_id', 'ACCOUNT-2025-');
        $this->reorganizeTableIds('affiliate_partners', 'affiliate_partner_id', 'AP-2025-');
        $this->reorganizeTableIds('applicants', 'applicant_id', 'APPLICANT-2025-');
        $this->reorganizeTableIds('applications', 'application_id', 'APPLICATION-2025-');
        $this->reorganizeTableIds('budget_updates', 'budget_update_id', 'BDG-UPD-2025-');
        $this->reorganizeTableIds('clients', 'client_id', 'CLIENT-2025-');
        $this->reorganizeTableIds('contacts', 'contact_id', 'CONTACT-2025-');
        $this->reorganizeTableIds('data', 'data_id', 'DATA-2025-');
        $this->reorganizeTableIds('expense_ranges', 'exp_range_id', 'EXP-RANGE-2025-');
        $this->reorganizeTableIds('files', 'file_id', 'FILE-2025-');
        $this->reorganizeTableIds('guarantee_letters', 'gl_id', 'GL-2025-');
        $this->reorganizeTableIds('households', 'household_id', 'HOUSEHOLD-2025-');
        $this->reorganizeTableIds('logs', 'log_id', 'LOG-2025-');
        $this->reorganizeTableIds('members', 'member_id', 'MEMBER-2025-');
        $this->reorganizeTableIds('messages', 'message_id', 'MSG-2025-');
        $this->reorganizeTableIds('message_templates', 'msg_tmp_id', 'MSG-TMP-2025-');
        $this->reorganizeTableIds('occupations', 'occupation_id', 'OCCUPATION-2025-');
        $this->reorganizeTableIds('patients', 'patient_id', 'PATIENT-2025-');
        $this->reorganizeTableIds('reports', 'report_id', 'REPORT-2025-');
        $this->reorganizeTableIds('roles', 'role_id', 'ROLE-2025-');
        $this->reorganizeTableIds('services', 'service_id', 'SERVICE-2025-');
        $this->reorganizeTableIds('signers', 'signer_id', 'SIGNER-2025-');
        $this->reorganizeTableIds('sponsors', 'sponsor_id', 'SPONSOR-2025-');
        $this->reorganizeTableIds('staff', 'staff_id', 'STAFF-2025-');
        $this->reorganizeTableIds('tariff_lists', 'tariff_list_id', 'TARIFF-LIST-2025-');

        // 2. Re-enable foreign key checks now that all IDs have been updated.
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not implemented, as this is a reorganization logic.
    }

    private function reorganizeTableIds(string $originalTable, string $idColumn, string $idPrefix): void
    {
        // Tables and the foreign key columns that need to be updated
        $tables = [
            'accounts' => ['data_id'],
            'applications' => ['applicant_id', 'client_id', 'staff_id', 'service_id'],
            'budget_updates' => ['data_id', 'sponsor_id'],
            'clients' => ['member_id', 'occupation_id'],
            'contacts' => ['client_id'],
            'expense_ranges' => ['tariff_list_id', 'service_id'],
            'files' => ['data_id', 'member_id'],
            'guarantee_letters' => ['application_id', 'budget_update_id'],
            'households' => ['client_id'],
            'logs' => ['staff_id'],
            'members' => ['account_id'],
            'messages' => ['msg_tmp_id', 'staff_id', 'contact_id'],
            'message_templates' => ['data_id'],
            'occupations' => ['data_id'],
            'patients' => ['applicant_id', 'member_id'],
            'reports' => ['staff_id'], // Confirmed: Only staff_id foreign key constraint in SQL
            'roles' => ['data_id'],
            'services' => ['data_id'],
            'signers' => ['member_id'],
            'sponsors' => ['member_id'],
            'staff' => ['member_id', 'role_id'],
            'tariff_lists' => ['data_id'],
        ];

        foreach ($tables as $table => $columns) {
            foreach ($columns as $column) {
                if (
                    $column === $idColumn ||
                    ($originalTable === 'members' && $column === 'member_id') ||
                    ($originalTable === 'data' && $column === 'data_id')
                ) {

                    DB::table($table)
                        ->where($column, 'LIKE', $idPrefix.'%')
                        ->update([
                            $column => DB::raw("CONCAT(
                                SUBSTRING_INDEX(SUBSTRING_INDEX($column, '-', 3), '-', -2),
                                '-',
                                LPAD(
                                    SUBSTRING_INDEX(
                                        $column,
                                        '-',
                                        -1
                                    ),
                                    4,
                                    '0'
                                )
                            )"),
                        ]);
                }
            }
        }

        DB::statement("
            UPDATE `$originalTable` SET `$idColumn` = CONCAT(
                SUBSTRING_INDEX(SUBSTRING_INDEX(`$idColumn`, '-', 3), '-', -2),
                '-',
                LPAD(
                    SUBSTRING_INDEX(
                        `$idColumn`,
                        '-',
                        -1
                    ),
                    4,
                    '0'
                )
            ) WHERE `$idColumn` LIKE '$idPrefix%'
        ");
    }
};
