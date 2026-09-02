<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->unsignedTinyInteger('week')->nullable()->after('level')->comment('Syllabus week 1-15');
            // Store binary blob for zero-disk persistent lightweight storage
            $table->binary('file_blob')->nullable()->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['week', 'file_blob']);
        });
    }
};
