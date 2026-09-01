<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // SQLite/MySQL compatibility: ensure status is string and supports 'scheduled'
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('status_new')->default('draft')->after('status');
        });

        DB::table('announcements')->update([
            'status_new' => DB::raw("CASE status
                WHEN 'published' THEN 'published'
                WHEN 'archived'  THEN 'archived'
                ELSE 'draft'
            END")
        ]);

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    public function down(): void {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('status_old')->default('draft')->after('status');
        });

        DB::table('announcements')->update([
            'status_old' => DB::raw("CASE status
                WHEN 'published' THEN 'published'
                WHEN 'archived'  THEN 'archived'
                ELSE 'draft'
            END")
        ]);

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->renameColumn('status_old', 'status');
        });
    }
};
