<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            // offers(id,title,description,experience_years,category,country,city,remote?,type,image,industry) type CDD or CDI
            $table->string('title');
            $table->text('description');
            $table->integer('experience_years');
            $table->string('category');
            $table->string('country');
            $table->string('city');
            $table->string('status')->default('pending');
            $table->boolean('remote')->default(false);
            $table->string('type'); // CDD or CDI
            $table->string('industry');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
};
