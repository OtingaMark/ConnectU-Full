<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'skill_id')) {
                $table->foreignId('skill_id')
                    ->nullable()
                    ->after('feedback_id')
                    ->constrained('skills')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'skill_id')) {
                $table->dropConstrainedForeignId('skill_id');
            }
        });
    }
};
