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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade'); //User who uploaded/shared the resource

            $table->string('title'); //Resource title

            $table->text('description')->nullable(); //Short description of the resource

            $table->string('course')->nullable(); //Course/unit related to the resource

            $table->string('file_path')->nullable(); //Uploaded file path

            $table->string('resource_link')->nullable(); //External link if resource is online

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};