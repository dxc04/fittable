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
        Schema::create('job_posting_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Ensure a user can only be linked to a job posting once
            $table->unique(['job_posting_id', 'user_id']);
        });

        // Populate the pivot table from existing assessments
        DB::statement('
            INSERT INTO job_posting_user (job_posting_id, user_id, created_at, updated_at)
            SELECT DISTINCT job_posting_id, user_id, MIN(created_at), MAX(updated_at)
            FROM assessments
            GROUP BY job_posting_id, user_id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posting_user');
    }
};
