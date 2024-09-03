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
        Schema::create('timesheet_entries', function (Blueprint $table) {
            $table->id();
            // timesheet_entries(id,timesheet_id,date,work_duration)
            $table->unsignedBigInteger('timesheet_id');
            $table->date('date');
            $table->integer('day');
            $table->float('work_duration')->default(0);
            $table->foreign('timesheet_id')->references('id')->on('timesheets')->onDelete('cascade');
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
        Schema::dropIfExists('timesheet_entries');
    }
};
