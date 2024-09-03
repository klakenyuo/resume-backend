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
        Schema::create('candidats', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('email_s')->nullable();
            $table->string('telephone')->nullable();
            $table->string('telephone_s')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('adress')->nullable();
            $table->string('last_situation')->nullable();
            $table->string('entreprise_id')->nullable();
            $table->string('current_client_id')->nullable();
            $table->string('contrat_type')->nullable();
            $table->string('contrat_start')->nullable();
            $table->string('contrat_end')->nullable();
            $table->string('tjm')->nullable();
            $table->string('sal_net')->nullable();
            $table->string('sal_brut')->nullable();
            $table->string('status_ano')->nullable();
            $table->string('status')->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('candidats');
    }
};
