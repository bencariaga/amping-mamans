<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('applicants', 'created_at')) {
            Schema::table('applicants', function (Blueprint $table) {
                $table->timestamp('created_at')->useCurrent()->after('phic_category');
            });

            // Backfill from ID prefix: APPLICANT-YYYY-#########
            DB::statement("UPDATE applicants SET created_at = STR_TO_DATE(CONCAT(SUBSTRING(applicant_id, 11, 4), '-01-01 00:00:00'), '%Y-%m-%d %H:%i:%s') WHERE created_at IS NULL");
        }

        if (!Schema::hasColumn('patients', 'created_at')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->timestamp('created_at')->useCurrent()->after('patient_category');
            });

            // Backfill from ID prefix: PATIENT-YYYY-#########
            DB::statement("UPDATE patients SET created_at = STR_TO_DATE(CONCAT(SUBSTRING(patient_id, 9, 4), '-01-01 00:00:00'), '%Y-%m-%d %H:%i:%s') WHERE created_at IS NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('applicants', 'created_at')) {
            Schema::table('applicants', function (Blueprint $table) {
                $table->dropColumn('created_at');
            });
        }

        if (Schema::hasColumn('patients', 'created_at')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropColumn('created_at');
            });
        }
    }
};
