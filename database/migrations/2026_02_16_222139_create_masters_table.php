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
        Schema::create('masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->json('name_i18n')->nullable();
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->json('bio_i18n')->nullable();
            $table->string('avatar')->nullable();
            $table->unsignedSmallInteger('experience_years')->nullable();
            $table->json('specialties')->nullable();
            $table->json('specialties_i18n')->nullable();
            $table->json('languages')->nullable();
            $table->json('certificates')->nullable();
            $table->json('certificates_i18n')->nullable();
            $table->string('instagram')->nullable();
            $table->json('schedule_rules')->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masters');
    }
};
