<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('direction')->default('outgoing'); // outgoing, incoming
            $table->string('mailer')->default('simulated');   // smtp, simulated, log
            $table->string('template')->nullable();           // welcome, security, broadcast, inquiry, etc.
            $table->string('recipient');
            $table->string('sender');
            $table->string('subject');
            $table->longText('body_html');
            $table->string('status')->default('simulated');   // delivered, failed, simulated, received
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
