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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            // clients(name,website,domain,country,city,adress,contact,telephone,email)
            $table->string('name');
            $table->string('website')->nullable();
            $table->string('domain')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('adress')->nullable();
            $table->string('contact')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();    
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
        Schema::dropIfExists('clients');
    }
};
