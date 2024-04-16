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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_id');
            $table->unsignedBigInteger('job_id');
            $table->dateTime('application_date')->default(now()); // Fecha de aplicación completada automáticamente
            $table->dateTime('rejection_date')->nullable(); // Fecha de rechazo
            $table->text('cover_letter')->nullable();
            $table->enum('status', ['Pending', 'Reviewed', 'Accepted', 'Rejected', 'Expired'])->default('Pending')->nullable();
            $table->timestamps();

            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
