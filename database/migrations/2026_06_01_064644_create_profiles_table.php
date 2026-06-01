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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); //connect profile to user
            $table->string('course'); //student course
            $table->text('bio')->nullable(); // Short bio/about section
            $table->text('interests')->nullable(); // Academic interests
            $table->text('skills')->nullable(); // Skills student can share
            $table->string('availability')->nullable(); // Availability status
            $table->string('profile_picture')->nullable(); // URL or path to profile picture


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
