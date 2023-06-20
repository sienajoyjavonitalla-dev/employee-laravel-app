<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('ContactID')->nullable()->index(); 
            $table->string('ContactStatus')->nullable(); 
            $table->string('Name')->nullable();
            $table->string('POBOX_AddressLine1')->nullable();
            $table->string('POBOX_City')->nullable();
            $table->string('POBOX_Region')->nullable();
            $table->string('POBOX_PostalCode')->nullable();
            $table->string('POBOX_Country')->nullable();
            $table->string('STREET_AddressLine1')->nullable();
            $table->string('STREET_City')->nullable();
            $table->string('STREET_Region')->nullable();
            $table->string('STREET_PostalCode')->nullable();
            $table->string('STREET_Country')->nullable();
            $table->string('PhoneNumber')->nullable();
            $table->string('PhoneAreaCode')->nullable();
            $table->string('PhoneCountryCode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('clients');
    }
}
