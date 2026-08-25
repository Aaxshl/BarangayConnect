<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // SQLite/MySQL compatibility: ensure status is string and add progressive tracking columns
        Schema::table('service_logs', function (Blueprint $table) {
            $table->text('resolution_notes')->nullable()->after('remarks');
            $table->text('cancellation_reason')->nullable()->after('resolution_notes');
            $table->timestamp('resolved_at')->nullable()->after('cancellation_reason');
            $table->timestamp('closed_at')->nullable()->after('resolved_at');
        });

        // Recreate status column to string to prevent enum restrictions
        Schema::table('service_logs', function (Blueprint $table) {
            $table->string('status_new')->default('pending')->after('status');
        });

        DB::table('service_logs')->update([
            'status_new' => DB::raw("CASE status
                WHEN 'pending'     THEN 'pending'
                WHEN 'in_progress' THEN 'in_progress'
                WHEN 'resolved'    THEN 'resolved'
                WHEN 'closed'      THEN 'closed'
                ELSE 'pending'
            END")
        ]);

        Schema::table('service_logs', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('service_logs', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    public function down(): void {
        Schema::table('service_logs', function (Blueprint $table) {
            $table->dropColumn([
                'resolution_notes',
                'cancellation_reason',
                'resolved_at',
                'closed_at'
            ]);
        });
    }
};
