<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Make feedback.rating nullable so app feedback can be submitted without a score.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE feedback MODIFY rating INT NULL');
    }

    /**
     * Restore feedback.rating as NOT NULL.
     */
    public function down(): void
    {
        DB::statement('UPDATE feedback SET rating = 3 WHERE rating IS NULL');
        DB::statement('ALTER TABLE feedback MODIFY rating INT NOT NULL');
    }
};
