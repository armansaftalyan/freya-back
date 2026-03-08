<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users');
            $table->foreignId('master_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->constrained();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('status')->default('pending')->comment('pending, confirmed, cancelled, done, no_show');
            $table->text('comment')->nullable();
            $table->string('source')->default('site');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['master_id', 'start_at', 'end_at']);
            $table->index(['client_id', 'start_at']);
            $table->index(['status']);
        });

        Schema::create('appointment_service', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('duration_minutes');
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_service');
        Schema::dropIfExists('appointments');
    }
};
