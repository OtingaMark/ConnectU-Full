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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();

            $table->foreignId('giver_id')->constrained('users')->onDelete('cascade'); //User giving feedback

            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade'); //User receiving feedback

            $table->integer('rating'); //Rating score

            $table->text('comment')->nullable(); //Feedback comment

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};