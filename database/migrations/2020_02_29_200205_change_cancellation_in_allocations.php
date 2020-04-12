<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCancellationInAllocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allocations', function (Blueprint $table) {
            $table->dropForeign(['cancel_user_id']);
            $table->dropColumn('cancel_user_id');

            $table->dropColumn('cancel_date');

            $table->unsignedBigInteger('cancellation_id')->nullable()->after('user_id');
            $table->foreign('cancellation_id')->references('id')->on('allocation_cancellations')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allocations', function (Blueprint $table) {
            $table->unsignedBigInteger('cancel_user_id')->nullable()->after('user_id');
            $table->foreign('cancel_user_id')->references('id')->on('users')->onDelete('restrict');

            $table->datetime('cancel_date')->nullable()->after('cancel_user_id');
            $table->index('cancel_date');

            $table->dropForeign(['cancellation_id']);
            $table->dropColumn('cancellation_id');
        });
    }
}
