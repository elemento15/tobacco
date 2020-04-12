<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllocationCancellationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocation_cancellations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('cancel_date');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('comments')->nullable();
            $table->boolean('is_pending')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allocation_cancellations');
    }
}
