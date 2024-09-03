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
        Schema::table('candidats', function (Blueprint $table) {
            $table->longText('clients')->nullable();
            $table->longText('langues')->nullable();
            $table->longText('etl')->nullable();
            $table->longText('pretentions_salariales')->nullable();
            $table->longText('certifications')->nullable();
            $table->longText('gestion_projet')->nullable();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidats', function (Blueprint $table) {
            $table->dropColumn('clients');
            $table->dropColumn('langues');
            $table->dropColumn('etl');
            $table->dropColumn('pretentions_salariales');   
            $table->dropColumn('certifications');
            $table->dropColumn('gestion_projet');
        });
    }
};
