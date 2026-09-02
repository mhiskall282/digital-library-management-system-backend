<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['NEW_RESOURCE', 'SYSTEM', 'UPDATE', 'GENERAL'])->default('GENERAL')->index();
            $table->string('title');
            $table->text('message');
            $table->foreignId('resource_id')->nullable()->constrained('resources')->nullOnDelete();
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false)->index();
            $table->timestamps();

            $table->index(['user_id', 'is_read', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
