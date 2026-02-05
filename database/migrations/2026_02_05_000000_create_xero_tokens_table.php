<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXeroTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xero_tokens', function (Blueprint $table) {
            $table->id();
            $table->text('id_token')->nullable();
            $table->text('access_token');
            $table->unsignedInteger('expires_in')->nullable();
            $table->string('token_type', 50)->nullable();
            $table->text('refresh_token')->nullable();
            $table->text('scopes')->nullable();
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
        Schema::dropIfExists('xero_tokens');
    }
}
