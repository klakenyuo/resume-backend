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
        Schema::create('timesheet_entry_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('timesheet_entry_id');
            $table->unsignedBigInteger('project_id');
            $table->foreign('timesheet_entry_id')->references('id')->on('timesheet_entries')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->float('work_duration')->default(0);
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
        Schema::dropIfExists('timesheet_entry_projects');
    }
};
