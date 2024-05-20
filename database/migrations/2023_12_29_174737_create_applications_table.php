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
            $table->dateTime('application_date')->default(now()); // Automatically set the application date
            $table->dateTime('rejection_date')->nullable(); // Rejection date
            $table->text('cover_letter')->nullable();
            $table->enum('status', [
                'Sent',
                'Reviewed',
                'Evaluated',
                'Closed',
                'Not Continuing Process',
                'Continuing Process',
                'Preselected',
                'Virtual Interview',
                'In-Person Interview',
                'Test Application (In-Person, Virtual or On-Platform)',
                'Ready to Hire',
                'Process Closed'
            ])->default('Sent');
            $table->timestamps();

            $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
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
