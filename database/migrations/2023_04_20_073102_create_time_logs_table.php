<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->index();
            $table->string('assigned_id')->index();
            $table->string('start_time');
            $table->string('end_time')->nullable()->index();
            $table->string('date');
            $table->string('timesheet')->nullable();
            $table->boolean('lunch_break')->default(0);
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
        Schema::dropIfExists('time_logs');
    }
}
