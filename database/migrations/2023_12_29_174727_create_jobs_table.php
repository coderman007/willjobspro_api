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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('job_category_id');
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->dateTime('posted_date');
            $table->dateTime('deadline');
            $table->string('location');
            $table->decimal('salary', 10, 2);
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('experience_required')->nullable();
            $table->enum('status', ['Open', 'Closed', 'Under Review'])->default('Open');;
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('job_category_id')->references('id')->on('job_categories')->onDelete('cascade');
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
