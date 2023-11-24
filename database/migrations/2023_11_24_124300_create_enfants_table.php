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
        Schema::create('enfants', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->boolean('sexe');
            $table->integer('age');
            $table->integer('poids')->nullable();
            $table->string('allergie')->nullable();
            $table->string('taille')->nullable();
            $table->double('sys')->nullable();
            $table->double('dias')->nullable();
            $table->unsignedBigInteger('parentId');
            $table->foreign('parentId')->references('id')->on('patients');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enfants');
    }
};
