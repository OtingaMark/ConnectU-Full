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
    Schema::table('messages', function (Blueprint $table) {
        $table->string('file_path')->nullable()->after('message');
        $table->string('resource_link')->nullable()->after('file_path');
        $table->string('message_type')->default('text')->after('resource_link');
    });
}

public function down(): void
{
    Schema::table('messages', function (Blueprint $table) {
        $table->dropColumn(['file_path', 'resource_link', 'message_type']);
    });
}
};
