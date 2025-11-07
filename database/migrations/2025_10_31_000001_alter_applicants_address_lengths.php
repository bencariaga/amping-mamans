<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Widen address-related columns to prevent truncation
        DB::statement("ALTER TABLE `applicants` MODIFY `subdivision` VARCHAR(50) NULL");
        DB::statement("ALTER TABLE `applicants` MODIFY `purok` VARCHAR(50) NULL");
        DB::statement("ALTER TABLE `applicants` MODIFY `sitio` VARCHAR(50) NULL");
        DB::statement("ALTER TABLE `applicants` MODIFY `street` VARCHAR(100) NOT NULL");
        DB::statement("ALTER TABLE `applicants` MODIFY `phase` VARCHAR(20) NULL");
        DB::statement("ALTER TABLE `applicants` MODIFY `block_number` VARCHAR(20) NULL");
        DB::statement("ALTER TABLE `applicants` MODIFY `house_number` VARCHAR(20) NULL");
    }

    public function down(): void
    {
        // Revert to original sizes from dump (use with caution; may truncate data)
        DB::statement("ALTER TABLE `applicants` MODIFY `subdivision` VARCHAR(20) NULL");
        DB::statement("ALTER TABLE `applicants` MODIFY `purok` VARCHAR(20) NULL");
        DB::statement("ALTER TABLE `applicants` MODIFY `sitio` VARCHAR(20) NULL");
        DB::statement("ALTER TABLE `applicants` MODIFY `street` VARCHAR(20) NOT NULL");
        DB::statement("ALTER TABLE `applicants` MODIFY `phase` VARCHAR(10) NULL");
        DB::statement("ALTER TABLE `applicants` MODIFY `block_number` VARCHAR(10) NULL");
        DB::statement("ALTER TABLE `applicants` MODIFY `house_number` VARCHAR(10) NULL");
    }
};
