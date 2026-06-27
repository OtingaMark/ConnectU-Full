<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('active')->after('role');
            $table->text('suspension_reason')->nullable()->after('status');
            $table->timestamp('suspended_at')->nullable()->after('suspension_reason');
        });

        DB::table('users')
            ->whereRaw('LOWER(TRIM(role)) = ?', ['suspended'])
            ->update([
                'status' => 'suspended',
                'suspension_reason' => 'Migrated from legacy suspended role.',
                'suspended_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'suspension_reason', 'suspended_at']);
        });
    }
};
