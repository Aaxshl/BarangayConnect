<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'viewed_at')) {
                $table->timestamp('viewed_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('documents', 'released_at')) {
                $table->timestamp('released_at')->nullable()->after('viewed_at');
            }
            if (!Schema::hasColumn('documents', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('remarks');
            }
        });
    }

    public function down(): void {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'viewed_at')) {
                $table->dropColumn('viewed_at');
            }
            if (Schema::hasColumn('documents', 'released_at')) {
                $table->dropColumn('released_at');
            }
            if (Schema::hasColumn('documents', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
