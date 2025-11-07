<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column already exists
        if (!Schema::hasColumn('staff', 'username')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->string('username', 50)->nullable()->after('role_id');
            });
        }
        
        // Update existing records with a temporary username based on their staff_id
        DB::table('staff')->whereNull('username')->orWhere('username', '')->get()->each(function ($staff) {
            DB::table('staff')
                ->where('staff_id', $staff->staff_id)
                ->update(['username' => 'user_' . substr($staff->staff_id, -9)]);
        });
        
        // Now make username unique and not nullable
        Schema::table('staff', function (Blueprint $table) {
            $table->string('username', 50)->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
