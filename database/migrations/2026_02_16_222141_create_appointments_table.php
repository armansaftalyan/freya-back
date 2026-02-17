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
            $table->foreignId('master_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('service_id')->constrained();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('status')->default('pending')->comment('pending, confirmed, cancelled, done, no_show');
            $table->text('comment')->nullable();
            $table->string('source')->default('site');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};