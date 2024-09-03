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
        Schema::table('users', function (Blueprint $table) {
            // society,entry_date,birth_date,birth_place,nationality,adress,iban,tel_one,tel_two,comments,tjm
            $table->string('society')->nullable();
            $table->date('entry_date')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('nationality')->nullable();
            $table->string('adress')->nullable();
            $table->string('iban')->nullable();
            $table->string('tel_one')->nullable();
            $table->string('tel_two')->nullable();
            $table->text('comments')->nullable();
            $table->double('tjm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('society');
            $table->dropColumn('entry_date');
            $table->dropColumn('birth_date');
            $table->dropColumn('birth_place');
            $table->dropColumn('nationality');
            $table->dropColumn('adress');
            $table->dropColumn('iban');
            $table->dropColumn('tel_one');
            $table->dropColumn('tel_two');
            $table->dropColumn('comments');
            $table->dropColumn('tjm');
        });
    }
};
