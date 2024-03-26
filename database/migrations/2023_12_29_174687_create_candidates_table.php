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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('full_name');
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('work_experience')->nullable();
            $table->text('certifications')->nullable();
            $table->text('references')->nullable();
            $table->decimal('expected_salary', 10, 2)->nullable();
            $table->string('cv_path')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->json('social_networks')->nullable();
            $table->enum('status', ['Active', 'Blocked'])->nullable()->default('Active');
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
