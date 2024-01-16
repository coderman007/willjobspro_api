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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('full_name');
            $table->string('gender');
            $table->date('date_of_birth')->nullable();
            $table->string('address');
            $table->string('phone_number');
            $table->text('work_experience')->nullable();
            $table->text('education')->nullable();
            $table->text('skills')->nullable();
            $table->text('certifications')->nullable();
            $table->text('languages')->nullable();
            $table->text('references')->nullable();
            $table->decimal('expected_salary', 10, 2)->nullable();
            $table->string('cv_path')->nullable()->default('N/A');
            $table->string('photo_path')->nullable()->default('N/A');
            $table->string('banner_path')->nullable()->default('N/A');
            $table->enum('status', ['Active', 'Inactive']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
