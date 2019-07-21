<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('mov_date');
            $table->enum('type', ['E','S','T']);
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('transfer_to')->nullable();
            $table->unsignedBigInteger('transfer_from')->nullable();
            $table->boolean('active')->default(1);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('cancel_user_id')->nullable();
            $table->datetime('cancel_date')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->index('mov_date');
            $table->index('cancel_date');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('cancel_user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movements');
    }
}
