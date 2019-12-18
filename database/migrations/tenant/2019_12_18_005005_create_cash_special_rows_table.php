<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashSpecialRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_special_rows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('msrp')->default(0);
            $table->decimal('discount')->default(0);
            $table->integer('cash_special_row_name_id');
            $table->integer('cash_special_column_id');
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
        Schema::dropIfExists('cash_special_rows');
    }
}
