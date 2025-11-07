<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for members table
        Schema::table('members', function (Blueprint $table) {
            $table->index(['first_name', 'last_name'], 'idx_members_name');
            $table->index('full_name', 'idx_members_full_name');
            $table->index('account_id', 'idx_members_account_id');
        });

        // Add indexes for clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->index('member_id', 'idx_clients_member_id');
        });

        // Add indexes for applicants table
        Schema::table('applicants', function (Blueprint $table) {
            $table->index('client_id', 'idx_applicants_client_id');
        });

        // Add indexes for patients table
        Schema::table('patients', function (Blueprint $table) {
            $table->index('client_id', 'idx_patients_client_id');
            $table->index('applicant_id', 'idx_patients_applicant_id');
        });

        // Add indexes for sponsors table
        Schema::table('sponsors', function (Blueprint $table) {
            $table->index('member_id', 'idx_sponsors_member_id');
        });

        // Add indexes for budget_updates table
        Schema::table('budget_updates', function (Blueprint $table) {
            $table->index('sponsor_id', 'idx_budget_updates_sponsor_id');
            $table->index('data_id', 'idx_budget_updates_data_id');
            $table->index(['possessor', 'reason'], 'idx_budget_updates_possessor_reason');
        });

        // Add indexes for tariff_lists table
        Schema::table('tariff_lists', function (Blueprint $table) {
            $table->index('data_id', 'idx_tariff_lists_data_id');
            $table->index('effectivity_date', 'idx_tariff_lists_effectivity_date');
        });

        // Add indexes for expense_ranges table
        Schema::table('expense_ranges', function (Blueprint $table) {
            $table->index('tariff_list_id', 'idx_expense_ranges_tariff_list_id');
            $table->index('service_id', 'idx_expense_ranges_service_id');
        });

        // Add indexes for applications table
        Schema::table('applications', function (Blueprint $table) {
            $table->index('applicant_id', 'idx_applications_applicant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from members table
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex('idx_members_name');
            $table->dropIndex('idx_members_full_name');
            $table->dropIndex('idx_members_account_id');
        });

        // Drop indexes from clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('idx_clients_member_id');
        });

        // Drop indexes from applicants table
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropIndex('idx_applicants_client_id');
        });

        // Drop indexes from patients table
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('idx_patients_client_id');
            $table->dropIndex('idx_patients_applicant_id');
        });

        // Drop indexes from sponsors table
        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropIndex('idx_sponsors_member_id');
        });

        // Drop indexes from budget_updates table
        Schema::table('budget_updates', function (Blueprint $table) {
            $table->dropIndex('idx_budget_updates_sponsor_id');
            $table->dropIndex('idx_budget_updates_data_id');
            $table->dropIndex('idx_budget_updates_possessor_reason');
        });

        // Drop indexes from tariff_lists table
        Schema::table('tariff_lists', function (Blueprint $table) {
            $table->dropIndex('idx_tariff_lists_data_id');
            $table->dropIndex('idx_tariff_lists_effectivity_date');
        });

        // Drop indexes from expense_ranges table
        Schema::table('expense_ranges', function (Blueprint $table) {
            $table->dropIndex('idx_expense_ranges_tariff_list_id');
            $table->dropIndex('idx_expense_ranges_service_id');
        });

        // Drop indexes from applications table
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('idx_applications_applicant_id');
        });
    }
};
