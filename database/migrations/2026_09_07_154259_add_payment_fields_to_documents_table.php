<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->decimal('fee', 8, 2)->default(0.00)->after('number_of_copies');
            $table->string('payment_method')->default('cash')->after('fee'); // cash, gcash, free
            $table->enum('payment_status', ['unpaid', 'pending_verification', 'verified', 'declined', 'waived'])->default('unpaid')->after('payment_method');
            $table->string('payment_reference')->nullable()->after('payment_status');
            $table->string('payment_proof')->nullable()->after('payment_reference');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_proof');
            $table->foreignId('payment_verified_by')->nullable()->after('payment_verified_at')->constrained('users')->nullOnDelete();
            $table->text('payment_notes')->nullable()->after('payment_verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['payment_verified_by']);
            $table->dropColumn([
                'fee',
                'payment_method',
                'payment_status',
                'payment_reference',
                'payment_proof',
                'payment_verified_at',
                'payment_verified_by',
                'payment_notes'
            ]);
        });
    }
};
