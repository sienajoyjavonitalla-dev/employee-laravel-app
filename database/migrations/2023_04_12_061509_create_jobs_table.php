<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->index();
            $table->string('employee_id')->nullable()->index();
            $table->string('po_number')->nullable()->index();
            $table->string('status')->default('open')->index();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('start_date_time');
            $table->string('end_date_time');
            $table->string('address');
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
        Schema::dropIfExists('jobs');
    }
}
