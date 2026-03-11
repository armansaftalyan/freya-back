<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gift_card_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('buyer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable()->index();
            $table->string('recipient_phone', 32)->nullable()->index();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 8)->default('AMD');
            $table->string('payment_provider')->default('manual');
            $table->string('provider_payment_id')->nullable()->index();
            $table->string('status')->default('pending')->index();
            $table->timestamp('paid_at')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('gift_cards', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('qr_token', 96)->unique();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('gift_card_order_id')->nullable()->constrained('gift_card_orders')->nullOnDelete();
            $table->decimal('initial_amount', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->string('currency', 8)->default('AMD');
            $table->string('status')->default('active')->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['owner_user_id', 'status']);
        });

        Schema::create('gift_card_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('gift_card_id')->constrained('gift_cards')->cascadeOnDelete();
            $table->string('type')->index();
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->foreignId('performed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('booking_order_id')->nullable()->constrained('booking_orders')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['gift_card_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_card_transactions');
        Schema::dropIfExists('gift_cards');
        Schema::dropIfExists('gift_card_orders');
    }
};
