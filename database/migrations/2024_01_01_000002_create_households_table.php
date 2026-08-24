<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->string('household_id')->unique();
            $table->unsignedBigInteger('head_resident_id')->nullable();
            $table->string('address');
            $table->string('purok')->nullable();
            $table->string('zone')->nullable();
            $table->string('contact_number')->nullable();
            $table->integer('number_of_members')->default(0);
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('households'); }
};
