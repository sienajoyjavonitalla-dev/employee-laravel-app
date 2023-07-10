<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminFootprintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_footprints', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('action_type');
            $table->integer('entity_id')->index();
            $table->string('entity');
            $table->string('field')->nullable();
            $table->string('prev_value')->nullable();
            $table->string('new_value')->nullable();
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
        Schema::dropIfExists('admin_footprints');
    }
}
