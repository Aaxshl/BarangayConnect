<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('citizen_requests', function (Blueprint $table) {
            $table->string('priority')->default('medium')->after('request_type'); // low, medium, high, urgent
        });
    }

    public function down(): void {
        Schema::table('citizen_requests', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
};
