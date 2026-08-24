<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('birthdate')->nullable();
            $table->integer('age')->nullable();
            $table->enum('gender',['male','female']);
            $table->enum('civil_status',['single','married','widowed','separated']);
            $table->text('address');
            $table->string('purok')->nullable();
            $table->string('zone')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('password')->nullable();
            $table->string('occupation')->nullable();
            $table->foreignId('household_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status',['active','inactive'])->default('active');
            $table->string('qr_code')->nullable();
            $table->string('photo')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('residents'); }
};
