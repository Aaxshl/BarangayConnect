<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->foreignId('resident_id')->constrained()->cascadeOnDelete();
            $table->string('document_type');
            $table->string('purpose');
            $table->integer('number_of_copies')->default(1);
            $table->date('issue_date');
            $table->enum('status',['processing','released','pending_pickup','cancelled'])->default('processing');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('documents'); }
};
