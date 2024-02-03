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
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('skill_category_id');

            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            // Índice único para evitar duplicados en una categoría
            $table->unique(['skill_category_id', 'name']);
            $table->foreign('skill_category_id')->references('id')->on('skill_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
