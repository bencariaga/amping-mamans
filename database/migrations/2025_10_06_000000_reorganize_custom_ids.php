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
        $this->reorganizeTableIds('messages', 'message_id', 'MESSAGE-2025-');
        $this->reorganizeTableIds('message_templates', 'msg_tmp_id', 'MSG-TMP-2025-');
        $this->reorganizeTableIds('occupations', 'occupation_id', 'OCCUP-2025-');
        $this->reorganizeTableIds('patients', 'patient_id', 'PATIENT-2025-');
        $this->reorganizeTableIds('reports', 'report_id', 'REPORT-2025-');
        $this->reorganizeTableIds('roles', 'role_id', 'ROLE-2025-');
        $this->reorganizeTableIds('services', 'service_id', 'SERVICE-2025-');
        $this->reorganizeTableIds('signers', 'signer_id', 'SIGNER-2025-');
        $this->reorganizeTableIds('sponsors', 'sponsor_id', 'SPONSOR-2025-');
        $this->reorganizeTableIds('staff', 'staff_id', 'STAFF-2025-');
        $this->reorganizeTableIds('tariff_lists', 'tariff_list_id', 'TL-2025-');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it reorganizes IDs
        // Rolling back would require storing original IDs, which is complex
    }

    /**
     * Reorganize IDs for a specific table
     */
    private function reorganizeTableIds(string $tableName, string $idColumn, string $prefix): void
    {
        // Get all records ordered by creation date or original ID to maintain order
        $records = DB::table($tableName)
            ->orderBy($idColumn)
            ->get();

        $counter = 1;

        foreach ($records as $record) {
            $oldId = $record->{$idColumn};
            $newId = $prefix.Str::padLeft($counter, 9, '0');

            if ($oldId !== $newId) {
                // Update the record with new ID
                DB::table($tableName)
                    ->where($idColumn, $oldId)
                    ->update([$idColumn => $newId]);

                // Update all foreign key references in other tables
                $this->updateForeignKeyReferences($tableName, $idColumn, $oldId, $newId);
            }

            $counter++;
        }
    }

    /**
     * Update foreign key references across all tables
     */
    private function updateForeignKeyReferences(string $originalTable, string $idColumn, string $oldId, string $newId): void
    {
        $tables = [
            'accounts' => ['data_id'],
            'affiliate_partners' => ['account_id'],
            'applicants' => ['client_id'],
            'applications' => ['applicant_id', 'patient_id', 'affiliate_partner_id', 'exp_range_id'],
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
            'reports' => ['staff_id', 'file_id'],
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
                        ->where($column, $oldId)
                        ->update([$column => $newId]);
                }
            }
        }
    }
};
