<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubcontractorInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractor_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->index();
            $table->integer('bank_id')->index();
            $table->integer('subcontractor_id');
            $table->string('from_date');
            $table->string('to_date');
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
        Schema::dropIfExists('subcontractor_invoices');
    }
}
