<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // SQLite doesn't support MODIFY COLUMN / ENUM, so we:
        // 1. Add a new temporary string column
        // 2. Copy data over with value mapping
        // 3. Drop the old enum column
        // 4. Rename the new column

        Schema::table('documents', function (Blueprint $table) {
            $table->string('status_new')->default('pending')->after('status');
        });

        // Migrate old enum values → new status values
        DB::table('documents')->update([
            'status_new' => DB::raw("CASE status
                WHEN 'processing'    THEN 'processing'
                WHEN 'released'      THEN 'released'
                WHEN 'pending_pickup' THEN 'ready_for_pickup'
                WHEN 'cancelled'     THEN 'cancelled'
                ELSE 'pending'
            END")
        ]);

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    public function down(): void {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('status_old')->default('processing')->after('status');
        });

        DB::table('documents')->update([
            'status_old' => DB::raw("CASE status
                WHEN 'pending'           THEN 'processing'
                WHEN 'under_review'      THEN 'processing'
                WHEN 'processing'        THEN 'processing'
                WHEN 'ready_for_pickup'  THEN 'pending_pickup'
                WHEN 'released'          THEN 'released'
                ELSE 'cancelled'
            END")
        ]);

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->renameColumn('status_old', 'status');
        });
    }
};
