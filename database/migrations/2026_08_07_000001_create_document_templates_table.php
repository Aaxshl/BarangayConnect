<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('document_type')->unique();
            $table->string('title');
            $table->text('header_text')->nullable();
            $table->text('body_template');
            $table->text('footer_text')->nullable();
            $table->boolean('show_logo')->default(true);
            $table->string('custom_logo')->nullable();
            $table->string('signatory_title')->nullable()->default('Barangay Captain');
            $table->string('signatory_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('document_templates');
    }
};
