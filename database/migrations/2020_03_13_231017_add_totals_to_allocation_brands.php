<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalsToAllocationBrands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allocation_brands', function (Blueprint $table) {
            $table->decimal('total_cost', 12, 4)->default(0);
            $table->decimal('total_price', 12, 4)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allocation_brands', function (Blueprint $table) {
            $table->dropColumn('total_cost');
            $table->dropColumn('total_price');
        });
    }
}
