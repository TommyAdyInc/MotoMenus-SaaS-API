<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_information', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('deal_id');
            $table->decimal('msrp');
            $table->decimal('price');
            $table->decimal('manufacturer_freight')->nullable();
            $table->decimal('technician_setup')->nullable();
            $table->decimal('accessories')->nullable();
            $table->decimal('accessories_labor')->nullable();
            $table->decimal('labor')->nullable();
            $table->decimal('riders_edge_course')->nullable();
            $table->decimal('miscellaneous_costs')->nullable();
            $table->decimal('document_fee');
            $table->decimal('trade_in_allowance')->nullable();
            $table->decimal('sales_tax_rate', 5, 3);
            $table->decimal('payoff_balance_owed')->nullable();
            $table->decimal('title_trip_fee')->nullable();
            $table->decimal('deposit')->nullable();
            $table->boolean('show_msrp_on_pdf')->default(false);
            $table->boolean('taxable_price')->default(true);
            $table->boolean('taxable_manufacturer_freight')->default(true);
            $table->boolean('taxable_technician_setup')->default(true);
            $table->boolean('taxable_accessories')->default(true);
            $table->boolean('taxable_accessories_labor')->default(true);
            $table->boolean('taxable_labor')->default(true);
            $table->boolean('taxable_riders_edge_course')->default(true);
            $table->boolean('taxable_miscellaneous_costs')->default(false);
            $table->boolean('taxable_document_fee')->default(false);
            $table->boolean('tax_credit_on_trade')->default(false);
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
        Schema::dropIfExists('purchase_information');
    }
}
