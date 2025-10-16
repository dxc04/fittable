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
        Schema::table('assessments', function (Blueprint $table) {
            $table->json('application_strategy')->nullable();
            $table->json('interview_preparation')->nullable();
            $table->json('personalized_recommendation')->nullable();
            $table->dropColumn('recommendation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->text('recommendation')->nullable();
            $table->dropColumn(['application_strategy', 'interview_preparation', 'personalized_recommendation']);
        });
    }
};
