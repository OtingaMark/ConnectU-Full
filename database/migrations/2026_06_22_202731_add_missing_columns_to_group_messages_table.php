<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('group_messages', 'resource_link')) {
                $table->string('resource_link')->nullable()->after('file_path');
            }

            if (!Schema::hasColumn('group_messages', 'message_type')) {
                $table->string('message_type')->default('text')->after('resource_link');
            }
        });
    }

    public function down(): void
    {
        Schema::table('group_messages', function (Blueprint $table) {
            if (Schema::hasColumn('group_messages', 'resource_link')) {
                $table->dropColumn('resource_link');
            }

            if (Schema::hasColumn('group_messages', 'message_type')) {
                $table->dropColumn('message_type');
            }
        });
    }
};