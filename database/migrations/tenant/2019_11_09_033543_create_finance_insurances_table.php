<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceInsurancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_insurances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('cash_down_payment')->nullable();
            $table->decimal('preferred_standard_rate')->nullable();
            $table->mediumInteger('preferred_standard_term')->nullable();
            $table->decimal('promotional_rate')->nullable();
            $table->mediumInteger('promotional_term')->nullable();
            $table->decimal('full_protection')->nullable();
            $table->decimal('limited_protection')->nullable();
            $table->decimal('tire_wheel')->nullable();
            $table->decimal('gap_coverage')->nullable();
            $table->decimal('theft')->nullable();
            $table->decimal('priority_maintenance')->nullable();
            $table->decimal('appearance_protection')->nullable();
            $table->integer('deal_id');
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
        Schema::dropIfExists('finance_insurances');
    }
}
