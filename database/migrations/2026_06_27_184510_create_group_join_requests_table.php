<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_join_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('study_group_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('status', ['pending', 'approved', 'declined'])
                ->default('pending');

            $table->timestamps();

            $table->unique(['study_group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_join_requests');
    }
};
