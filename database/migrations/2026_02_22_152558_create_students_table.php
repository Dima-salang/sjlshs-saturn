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
        Schema::create('students', function (Blueprint $table) {
            $table->string('lrn')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->string('gender');
            $table->string('grade_level');
            $table->unsignedBigInteger('adviser_id')->nullable();

            $table->foreign('section_id')->references('section_id')->on('sections')->onDelete('set null');
            $table->foreign('adviser_id')->references('id')->on('teachers')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }

};
