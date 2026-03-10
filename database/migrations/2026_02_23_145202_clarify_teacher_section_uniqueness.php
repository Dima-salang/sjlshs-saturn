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
        Schema::table('teachers', function (Blueprint $table) {
            $table->unique('section_id');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->unique('adviser_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropUnique(['section_id']);
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropUnique(['adviser_id']);
        });
    }
};
