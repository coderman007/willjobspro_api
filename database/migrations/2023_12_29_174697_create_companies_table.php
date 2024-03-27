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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();

            $table->string('contact_person');
            $table->string('phone_number')->nullable();
            $table->string('industry')->nullable();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->json('social_networks')->nullable();
            $table->enum('status', ['Active', 'Blocked'])->nullable()->default('Active');
            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
