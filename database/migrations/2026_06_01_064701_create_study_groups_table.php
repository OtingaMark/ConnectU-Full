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
        Schema::create('study_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); //Group creator
            $table->string('group_name'); //Group name
            $table->string('course'); //Related course/unit
            $table->text('description')->nullable(); //Group description
            $table->integer('max_members')->default(10); //maximum members allowed in the group
            $table->string('meeting_schedule')->nullable(); //meeting times/days
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_groups');
    }
};

            
            

            

      