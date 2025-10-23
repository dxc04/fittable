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
        Schema::table('job_postings', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->after('original_text');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->after('recommendation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn('closed_at');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('closed_at');
        });
    }
};
