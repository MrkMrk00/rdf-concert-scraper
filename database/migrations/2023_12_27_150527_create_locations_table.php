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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('addressCountry', 255)->nullable();
            $table->string('addressLocality', 255)->nullable();
            $table->string('addressRegion', 255)->nullable();
            $table->string('postalCode', 255)->nullable();
            $table->string('streetAddress', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
