<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            if (!Schema::hasColumn('skills', 'category')) {
                $table->string('category')->default('Other')->after('description');
            }

            if (!Schema::hasColumn('skills', 'skill_type')) {
                $table->string('skill_type')->default('teach')->after('category');
            }
        });

        Schema::table('feedback', function (Blueprint $table) {
            if (!Schema::hasColumn('feedback', 'feedback_type')) {
                $table->string('feedback_type')->default('peer')->after('receiver_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            if (Schema::hasColumn('skills', 'skill_type')) {
                $table->dropColumn('skill_type');
            }

            if (Schema::hasColumn('skills', 'category')) {
                $table->dropColumn('category');
            }
        });

        Schema::table('feedback', function (Blueprint $table) {
            if (Schema::hasColumn('feedback', 'feedback_type')) {
                $table->dropColumn('feedback_type');
            }
        });
    }
};
