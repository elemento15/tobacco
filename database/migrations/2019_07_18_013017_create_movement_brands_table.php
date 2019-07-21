<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovementBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movement_brands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('movement_id');
            $table->unsignedBigInteger('brand_id');
            $table->decimal('quantity', 12, 4);

            $table->foreign('movement_id')->references('id')->on('movements')->onDelete('restrict');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movement_brands');
    }
}
