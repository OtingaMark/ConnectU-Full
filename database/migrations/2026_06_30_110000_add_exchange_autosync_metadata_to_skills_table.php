<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            if (!Schema::hasColumn('skills', 'auto_created_from_exchange')) {
                $table->boolean('auto_created_from_exchange')
                    ->default(false)
                    ->after('collaboration_goal');
            }

            if (!Schema::hasColumn('skills', 'exchange_parent_skill_id')) {
                $table->foreignId('exchange_parent_skill_id')
                    ->nullable()
                    ->after('auto_created_from_exchange')
                    ->constrained('skills')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            if (Schema::hasColumn('skills', 'exchange_parent_skill_id')) {
                $table->dropConstrainedForeignId('exchange_parent_skill_id');
            }

            if (Schema::hasColumn('skills', 'auto_created_from_exchange')) {
                $table->dropColumn('auto_created_from_exchange');
            }
        });
    }
};
