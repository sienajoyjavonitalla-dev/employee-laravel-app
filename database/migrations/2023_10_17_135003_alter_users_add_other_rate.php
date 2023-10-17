<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersAddOtherRate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('other_rate_per_hour')->nullable()->after('rate_per_hour');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('other_rate_per_hour')->nullable()->after('rate_per_hour');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('other_rate_per_hour');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('other_rate_per_hour');
        });
    }
}
