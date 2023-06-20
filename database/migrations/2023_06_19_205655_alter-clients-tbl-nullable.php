<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClientsTblNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('client_name')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->integer('rate_per_hour', false, true)->nullable()->change();
            $table->integer('ot_rate_per_hour', false, true)->nullable()->change();
            $table->string('abn')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
