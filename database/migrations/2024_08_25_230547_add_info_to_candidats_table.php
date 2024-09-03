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
            $table->string('statut_matrimonial')->nullable();
            $table->string('annee_experience')->nullable();
            $table->longText('expertise_technique')->nullable();
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
            $table->dropColumn('statut_matrimonial');
            $table->dropColumn('annee_experience');
            $table->dropColumn('expertise_technique');
        });
    }
};
