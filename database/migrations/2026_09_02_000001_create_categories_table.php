<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('course_code')->index();
            $table->string('course_name');
            $table->enum('level', ['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'])->default('L100')->index();
            $table->enum('semester', ['FIRST', 'SECOND'])->default('FIRST')->index();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['course_code', 'level', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
