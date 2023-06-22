<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('invoice_id')->index();
            $table->string('invoice_number')->nullable()->index();
            $table->decimal('amount_due')->default('0.00');
            $table->decimal('amount_paid')->default('0.00');
            $table->decimal('amount_credited')->default('0.00');
            $table->boolean('has_attachments')->nullable();
            $table->string('date_string')->index();
            $table->string('duedate_string')->index();
            $table->string('branding_theme_id')->nullable()->index();
            $table->string('status')->index();
            $table->decimal('subtotal')->default('0.00');
            $table->decimal('TotalTax')->default('0.00');
            $table->decimal('Total')->default('0.00');
            $table->string('currency_code')->nullable();
            $table->string('updated_date_utc')->nullable();
            $table->string('fully_paid_date_utc')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
