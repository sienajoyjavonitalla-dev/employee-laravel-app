<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTimelogsAddInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_logs', function (Blueprint $table) {
            $table->integer('subbies_invoice_id')->nullable()->after('signature');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_logs', function (Blueprint $table) {
            $table->dropColumn('subbies_invoice_id');
        });
    }
}
