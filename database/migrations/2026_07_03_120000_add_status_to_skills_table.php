<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            if (!Schema::hasColumn('skills', 'status')) {
                $table->string('status')->default('active')->after('skill_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            if (Schema::hasColumn('skills', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
