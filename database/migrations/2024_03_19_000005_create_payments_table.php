<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('membership_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method');
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
}; 