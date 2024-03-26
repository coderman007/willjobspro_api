<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('education_level_job', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('education_level_id');
            $table->unsignedBigInteger('job_id');
            $table->timestamps();

            $table->foreign('education_level_id')->references('id')->on('education_levels')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_level_job');
    }
};
