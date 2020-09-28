<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAllocationWarehouseIdFromConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropForeign(['allocation_warehouse_id']);
            $table->dropColumn('allocation_warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->unsignedBigInteger('allocation_warehouse_id')->nullable();
            $table->foreign('allocation_warehouse_id')->references('id')->on('warehouses')->onDelete('restrict');
        });
    }
}
