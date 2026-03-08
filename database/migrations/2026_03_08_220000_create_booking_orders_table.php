<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained('users');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->text('comment')->nullable();
            $table->string('source')->default('site');
            $table->timestamps();

            $table->index(['client_id', 'created_at']);
            $table->index(['status']);
        });

        Schema::table('appointments', function (Blueprint $table): void {
            $table->foreignId('booking_order_id')
                ->nullable()
                ->after('id')
                ->constrained('booking_orders')
                ->nullOnDelete();

            $table->index(['booking_order_id', 'start_at']);
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('booking_order_id');
        });

        Schema::dropIfExists('booking_orders');
    }
};
