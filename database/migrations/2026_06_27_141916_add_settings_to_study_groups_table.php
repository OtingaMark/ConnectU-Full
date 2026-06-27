<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('study_groups', function (Blueprint $table) {
            $table->enum('visibility', ['public', 'private'])->default('public')->after('meeting_schedule');
            $table->boolean('requires_approval')->default(false)->after('visibility');
            $table->boolean('members_can_invite')->default(false)->after('requires_approval');
        });
    }

    public function down(): void
    {
        Schema::table('study_groups', function (Blueprint $table) {
            $table->dropColumn([
                'visibility',
                'requires_approval',
                'members_can_invite',
            ]);
        });
    }
};
