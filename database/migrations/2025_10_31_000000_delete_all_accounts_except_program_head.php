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
        // Get the Program Head account ID
        $programHeadAccountId = 'ACCOUNT-2025-AUG-0001';

        // Delete data from related tables in the correct order to maintain referential integrity

        // First, delete applications and related data
        DB::table('applications')->delete();

        // Delete guarantee letters
        DB::table('guarantee_letters')->delete();

        // Delete messages first (before contacts since messages reference contacts)
        DB::table('messages')->delete();

        // Delete audit logs
        DB::table('audit_logs')->delete();

        // Delete reports
        DB::table('reports')->delete();

        // Delete patients
        DB::table('patients')->delete();

        // Delete applicants
        DB::table('applicants')->delete();

        // Delete household members
        DB::table('household_members')->delete();

        // Delete households
        DB::table('households')->delete();

        // Now delete contacts (after messages are deleted)
        DB::table('contacts')
            ->whereNotIn('client_id', function ($query) use ($programHeadAccountId) {
                $query->select('clients.client_id')
                    ->from('clients')
                    ->join('members', 'clients.member_id', '=', 'members.member_id')
                    ->where('members.account_id', $programHeadAccountId);
            })
            ->delete();

        // Delete clients (except those linked to Program Head account)
        DB::table('clients')
            ->whereNotIn('member_id', function ($query) use ($programHeadAccountId) {
                $query->select('member_id')
                    ->from('members')
                    ->where('account_id', $programHeadAccountId);
            })
            ->delete();

        // Delete affiliate partners (except those linked to Program Head account)
        DB::table('affiliate_partners')
            ->where('account_id', '!=', $programHeadAccountId)
            ->delete();

        // Delete budget updates
        DB::table('budget_updates')->delete();

        // Delete sponsors (except those linked to Program Head account)
        DB::table('sponsors')
            ->whereNotIn('member_id', function ($query) use ($programHeadAccountId) {
                $query->select('member_id')
                    ->from('members')
                    ->where('account_id', $programHeadAccountId);
            })
            ->delete();

        // Delete signers (except those linked to Program Head account)
        DB::table('signers')
            ->whereNotIn('member_id', function ($query) use ($programHeadAccountId) {
                $query->select('member_id')
                    ->from('members')
                    ->where('account_id', $programHeadAccountId);
            })
            ->delete();

        // Delete staff (except Program Head)
        DB::table('staff')
            ->where('staff_id', '!=', 'STAFF-2025-01')
            ->delete();

        // Delete members (except those linked to Program Head account)
        DB::table('members')
            ->where('account_id', '!=', $programHeadAccountId)
            ->delete();

        // Finally, delete accounts (except Program Head account)
        DB::table('accounts')
            ->where('account_id', '!=', $programHeadAccountId)
            ->delete();

        // Delete data records that are no longer referenced (optional cleanup)
        // This keeps essential data like roles, services, occupations, etc.
        DB::table('data')
            ->whereNotIn('data_id', function ($query) {
                $query->select('data_id')->from('accounts')
                    ->union(DB::table('roles')->select('data_id'))
                    ->union(DB::table('services')->select('data_id'))
                    ->union(DB::table('occupations')->select('data_id'))
                    ->union(DB::table('tariff_lists')->select('data_id'))
                    ->union(DB::table('message_templates')->select('data_id'));
            })
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be safely reversed as it deletes data
        // You would need to restore from backup if you need to reverse this
    }
};
