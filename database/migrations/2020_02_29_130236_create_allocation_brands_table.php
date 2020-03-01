<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllocationBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocation_brands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('allocation_id');
            $table->unsignedBigInteger('brand_id');
            $table->decimal('quantity', 12, 4)->default(0);
            $table->decimal('unit_cost', 12, 4)->default(0);
            $table->decimal('unit_price', 12, 4)->default(0);

            $table->foreign('allocation_id')->references('id')->on('allocations')->onDelete('restrict');
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
        Schema::dropIfExists('allocation_brands');
    }
}
