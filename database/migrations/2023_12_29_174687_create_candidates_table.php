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

            $table->string('full_name');
            $table->string('gender');
            $table->date('date_of_birth')->nullable();
            $table->string('address');
            $table->string('phone_number');
            $table->text('work_experience')->nullable();
            $table->text('education')->nullable();
            $table->text('certifications')->nullable();
            $table->text('languages')->nullable();
            $table->text('references')->nullable();
            $table->decimal('expected_salary', 10, 2)->nullable();
            $table->text('cv_path')->nullable()->default('N/A');
            $table->text('photo_path')->nullable()->default('N/A');
            $table->text('banner_path')->nullable()->default('N/A');
            $table->json('social_networks')->nullable();
            $table->enum('status', ['Active', 'Inactive']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
