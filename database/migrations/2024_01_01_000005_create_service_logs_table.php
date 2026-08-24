<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('service_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_number')->unique();
            $table->string('service_type');
            $table->foreignId('resident_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description');
            $table->date('date_of_service');
            $table->enum('status',['pending','in_progress','resolved','closed'])->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('service_logs'); }
};
