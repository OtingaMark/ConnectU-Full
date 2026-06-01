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
        Schema::create('skills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade'); //Owner of the skill

            $table->string('skill_name'); //Name of the skill

            $table->text('description')->nullable(); //Skill description

            $table->string('skill_level')->default('Beginner'); //Beginner, Intermediate, Advanced

            $table->string('availability')->nullable(); //When user is available to teach/share

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};