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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unsignedBigInteger('job_category_id');
            $table->foreign('job_category_id')->references('id')->on('job_categories')->onDelete('cascade');
            $table->unsignedBigInteger('job_type_id');
            $table->foreign('job_type_id')->references('id')->on('job_types')->onDelete('cascade');
            $table->string('title');
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('set null');
            $table->text('description');
            $table->dateTime('posted_date');
            $table->dateTime('deadline');
            $table->string('location');
            $table->decimal('salary', 10, 2);
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->enum('status', ['Abierto', 'Cerrado', 'En Revisión']);

            $table->timestamps();
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
