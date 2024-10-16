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
            $table->string('date_naissance')->nullable();
            $table->string('preference_localisation')->nullable();
            $table->string('poste_actuel')->nullable();
            
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
            $table->dropColumn('date_naissance');
            $table->dropColumn('preference_localisation');
            $table->dropColumn('poste_actuel');
        });
    }
};
