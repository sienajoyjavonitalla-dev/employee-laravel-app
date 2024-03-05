<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersAddOtherOtRate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('other_ot_rate_per_hour')->nullable()->after('ot_rate_per_hour');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('other_ot_rate_per_hour')->nullable()->after('ot_rate_per_hour');
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
            $table->dropColumn('other_ot_rate_per_hour');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('other_ot_rate_per_hour');
        });
    }
}
