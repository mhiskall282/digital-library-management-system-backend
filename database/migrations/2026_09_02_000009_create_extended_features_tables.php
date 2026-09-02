<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extend Users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_onboarded')->default(true)->after('is_active');
            $table->integer('contributor_points')->default(0)->after('is_onboarded');
            $table->string('contributor_rank')->default('Novice')->after('contributor_points');
            $table->text('bio')->nullable()->after('contributor_rank');
            $table->string('phone')->nullable()->after('bio');
        });

        // 2. Extend Categories table with Program association
        Schema::table('categories', function (Blueprint $table) {
            $table->string('program')->default('General Business')->after('semester');
        });

        // 3. Extend Resources table with Status & Moderation
        Schema::table('resources', function (Blueprint $table) {
            $table->string('status')->default('APPROVED')->after('type'); // APPROVED, PENDING_REVIEW, REJECTED
            $table->text('rejection_reason')->nullable()->after('status');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('rejection_reason');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });

        // 4. Create Download Requests table (IP Tracking & Approval)
        Schema::create('download_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->text('reason');
            $table->string('status')->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // 5. Create Material Requests table (Student Help / Communication Desk)
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('course_code');
            $table->string('course_name');
            $table->string('program')->default('General Business');
            $table->string('level')->default('L100');
            $table->string('topic');
            $table->string('type')->default('SLIDE'); // SLIDE, PAST_QUESTION
            $table->string('urgency')->default('MEDIUM'); // LOW, MEDIUM, HIGH
            $table->string('status')->default('OPEN'); // OPEN, IN_PROGRESS, FULFILLED, CLOSED
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_requests');
        Schema::dropIfExists('download_requests');

        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['status', 'rejection_reason', 'reviewed_by', 'reviewed_at']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('program');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_onboarded', 'contributor_points', 'contributor_rank', 'bio', 'phone']);
        });
    }
};
