<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('masters', function (Blueprint $table): void {
            $table->json('schedule_rules')->nullable()->after('avatar');
        });

        Schema::table('master_service', function (Blueprint $table): void {
            $table->unique(['master_id', 'service_id']);
        });

        Schema::table('appointments', function (Blueprint $table): void {
            $table->index(['master_id', 'start_at', 'end_at']);
            $table->index(['client_id', 'start_at']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropIndex(['master_id', 'start_at', 'end_at']);
            $table->dropIndex(['client_id', 'start_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('master_service', function (Blueprint $table): void {
            $table->dropUnique(['master_id', 'service_id']);
        });

        Schema::table('masters', function (Blueprint $table): void {
            $table->dropColumn('schedule_rules');
        });
    }
};
