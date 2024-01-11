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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_location')->unsigned()->nullable()->index();
            $table->bigInteger('id_resource', unsigned: true)->index()->nullable(false);

            $table->string('name', 511)->nullable(false);
            $table->string('url', 511)->nullable();
            $table->string('image', 511)->nullable();
            $table->text('description')->nullable();
            $table->string('performer', 511)->nullable();
            $table->datetime('startDate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
