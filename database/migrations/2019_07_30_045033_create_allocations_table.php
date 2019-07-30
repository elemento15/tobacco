<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('rec_date');
            $table->unsignedBigInteger('salesperson_id');
            $table->enum('type', ['E','L','D']);
            $table->boolean('active')->default(1);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('cancel_user_id')->nullable();
            $table->datetime('cancel_date')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->index('rec_date');
            $table->index('cancel_date');
            $table->foreign('salesperson_id')->references('id')->on('salespeople')->onDelete('restrict');
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
        Schema::dropIfExists('allocations');
    }
}
