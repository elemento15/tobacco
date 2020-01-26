<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConceptIdToMovements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->unsignedBigInteger('concept_id')->after('warehouse_id');
            $table->foreign('concept_id')->references('id')->on('movement_concepts')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropForeign(['concept_id']);
            $table->dropColumn('concept_id');
        });
    }
}
