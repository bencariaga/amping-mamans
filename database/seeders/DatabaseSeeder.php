<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in dependency order to respect foreign key constraints
        $this->call([
            // Core data table (no dependencies)
            DataSeeder::class,
            
            // Account and member data (depends on data)
            AccountSeeder::class,
            MemberSeeder::class,
            
            // Role, staff, signers, sponsors (depends on data and members)
            RoleSeeder::class,
            StaffSeeder::class,
            SignerSeeder::class,
            SponsorSeeder::class,
            
            // Affiliate partners (depends on accounts)
            AffiliatePartnerSeeder::class,
            
            // Occupations and services (depends on data)
            OccupationSeeder::class,
            ServiceSeeder::class,
            
            // Clients (depends on members and occupations)
            ClientSeeder::class,
            
            // Contacts (depends on clients)
            ContactSeeder::class,
            
            // Applicants (depends on clients)
            ApplicantSeeder::class,
            
            // Patients (depends on clients and applicants)
            PatientSeeder::class,
            
            // Households and household members (depends on clients)
            HouseholdSeeder::class,
            HouseholdMemberSeeder::class,
            
            // Message templates (depends on data)
            MessageTemplateSeeder::class,
            
            // Tariff lists and expense ranges (depends on data and services)
            TariffListSeeder::class,
            ExpenseRangeSeeder::class,
            
            // Messages (depends on staff and contacts)
            MessageSeeder::class,
            
            // Applications (depends on patients, affiliate partners, expense ranges, messages)
            ApplicationSeeder::class,
            
            // Budget updates (depends on sponsors)
            BudgetUpdateSeeder::class,
            
            // Guarantee letters (depends on data, applications, sponsors)
            GuaranteeLetterSeeder::class,
        ]);
    }
}
