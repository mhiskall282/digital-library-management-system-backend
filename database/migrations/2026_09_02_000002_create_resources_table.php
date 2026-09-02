<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['SLIDE', 'PAST_QUESTION'])->default('SLIDE')->index();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->enum('level', ['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'])->default('L100')->index();
            $table->string('academic_year')->default('2023/2024');
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->default('application/pdf');
            $table->unsignedInteger('downloads')->default(0)->index();
            $table->decimal('average_rating', 3, 2)->default(0.00)->index();
            $table->unsignedInteger('total_reviews')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->index(['level', 'type']);
            $table->index(['category_id', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
