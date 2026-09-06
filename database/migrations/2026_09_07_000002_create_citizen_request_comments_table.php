<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('citizen_request_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('citizen_request_id')->constrained('citizen_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // for officials/staff
            $table->foreignId('resident_id')->nullable()->constrained('residents')->nullOnDelete(); // for resident submitter
            $table->enum('sender_type', ['official', 'resident'])->default('official');
            $table->text('message');
            $table->string('attachment')->nullable();
            $table->boolean('is_internal')->default(false); // private staff note vs public thread
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('citizen_request_comments');
    }
};
