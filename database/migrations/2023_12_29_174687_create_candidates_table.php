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
            $table->date('date_of_birth');
            $table->string('address');
            $table->string('phone_number');
            $table->text('work_experience');
            $table->text('education');
            $table->text('skills');
            $table->text('certifications');
            $table->text('languages');
            $table->text('references');
            $table->decimal('expected_salary', 10, 2);
            $table->string('cv_path')->nullable(); // Campo para almacenar la ruta del CV
            $table->string('photo_path')->nullable(); // Campo para almacenar la ruta de la imagen de perfil
            $table->enum('status', ['Activo', 'Inactivo', 'Pendiente']);
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
