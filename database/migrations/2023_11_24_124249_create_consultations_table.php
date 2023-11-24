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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('motif');
            $table->date('date_consultation');
            $table->unsignedBigInteger('patientId');
            $table->foreign('patientId')->references('id')->on('patients');
            $table->unsignedBigInteger('medecinId');
            $table->foreign('medecinId')->references('id')->on('medecins');
            $table->integer('status')->nullable();
            $table->string('rapport')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
