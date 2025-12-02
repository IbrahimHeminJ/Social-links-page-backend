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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('payment_id')->unique(); // FIB's unique payment ID
            $table->string('readable_code')->nullable();
            $table->longText('qr_code')->nullable(); // Base64 encoded QR code
            $table->timestamp('valid_until')->nullable();
            $table->string('personal_app_link')->nullable();
            $table->string('business_app_link')->nullable();
            $table->string('corporate_app_link')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('IQD');
            $table->string('status')->default('UNPAID'); // PAID, UNPAID, DECLINED
            $table->string('declining_reason')->nullable(); // SERVER_FAILURE, PAYMENT_EXPIRATION, PAYMENT_CANCELLATION
            $table->timestamp('declined_at')->nullable();
            $table->string('paid_by_name')->nullable();
            $table->string('paid_by_iban')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
