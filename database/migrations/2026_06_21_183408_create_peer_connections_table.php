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
        Schema::create('peer_connections', function (Blueprint $table) {
    $table->id();
    $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
    $table->string('status')->default('pending'); // pending, accepted, declined
    $table->timestamps();

    $table->unique(['requester_id', 'receiver_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peer_connections');
    }
};
