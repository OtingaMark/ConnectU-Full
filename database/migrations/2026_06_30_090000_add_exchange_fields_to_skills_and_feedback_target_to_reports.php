<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            if (!Schema::hasColumn('skills', 'exchange_skill_needed')) {
                $table->string('exchange_skill_needed')->nullable()->after('availability');
            }

            if (!Schema::hasColumn('skills', 'collaboration_goal')) {
                $table->string('collaboration_goal')->nullable()->after('exchange_skill_needed');
            }
        });

        DB::table('skills')
            ->where('skill_type', 'teach')
            ->update(['skill_type' => 'can_teach']);

        DB::table('skills')
            ->where('skill_type', 'learn')
            ->update(['skill_type' => 'want_to_learn']);

        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'feedback_id')) {
                $table->foreignId('feedback_id')
                    ->nullable()
                    ->after('direct_message_id')
                    ->constrained('feedback')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'feedback_id')) {
                $table->dropConstrainedForeignId('feedback_id');
            }
        });

        DB::table('skills')
            ->where('skill_type', 'can_teach')
            ->update(['skill_type' => 'teach']);

        DB::table('skills')
            ->where('skill_type', 'want_to_learn')
            ->update(['skill_type' => 'learn']);

        Schema::table('skills', function (Blueprint $table) {
            if (Schema::hasColumn('skills', 'collaboration_goal')) {
                $table->dropColumn('collaboration_goal');
            }

            if (Schema::hasColumn('skills', 'exchange_skill_needed')) {
                $table->dropColumn('exchange_skill_needed');
            }
        });
    }
};
