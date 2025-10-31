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
        // 1. Create the 'third_parties' table and add the required keys (Requests 1 & 2)
        Schema::create('third_parties', function (Blueprint $table) {
            // Request 1: Add primary key to "tp_id"
            // Assuming a custom string ID based on your other table IDs (like 'account_id' being VARCHAR(21)).
            $table->string('tp_id', 21)->primary();

            // Request 2: Add foreign key to "account_id"
            $table->string('account_id', 21);
            $table->foreign('account_id')->references('account_id')->on('accounts');

            // Add timestamps, which is always a good practice for auditing
            $table->timestamps();
        });

        // 2. Update 'affiliate_partners' table (Request 3)
        Schema::table('affiliate_partners', function (Blueprint $table) {
            // Drop the old 'account_id' foreign key and column
            // We assume the constraint was named by Laravel's convention (table_column_foreign)
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');

            // Add the new 'tp_id' column and foreign key
            // The column type must match the primary key on the 'third_parties' table
            $table->string('tp_id', 21);
            $table->foreign('tp_id')->references('tp_id')->on('third_parties');
        });

        // 3. Update 'sponsors' table (Request 4)
        Schema::table('sponsors', function (Blueprint $table) {
            // Drop the old 'member_id' foreign key and column
            // Based on your SQL dump, 'sponsors' has a foreign key to 'members'.
            // We drop the constraint and the column.
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');

            // Add the new 'tp_id' column and foreign key
            $table->string('tp_id', 21);
            $table->foreign('tp_id')->references('tp_id')->on('third_parties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert changes to 'sponsors' table
        Schema::table('sponsors', function (Blueprint $table) {
            // Drop the new foreign key and column
            $table->dropForeign(['tp_id']);
            $table->dropColumn('tp_id');

            // Re-add the old 'member_id' column and foreign key to 'members'
            // I'll make it nullable for a safer rollback, but you might adjust this.
            $table->string('member_id', 21)->nullable();
            $table->foreign('member_id')->references('member_id')->on('members');
        });

        // 2. Revert changes to 'affiliate_partners' table
        Schema::table('affiliate_partners', function (Blueprint $table) {
            // Drop the new foreign key and column
            $table->dropForeign(['tp_id']);
            $table->dropColumn('tp_id');

            // Re-add the old 'account_id' column and foreign key to 'accounts'
            $table->string('account_id', 21)->nullable();
            $table->foreign('account_id')->references('account_id')->on('accounts');
        });

        // 3. Drop the new 'third_parties' table
        Schema::dropIfExists('third_parties');
    }
};
