<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangesToMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropForeign(['cancel_user_id']);
            $table->dropColumn('cancel_user_id');
            $table->dropColumn('cancel_date');
            
            $table->boolean('is_pending')->default(0)->after('comments');
            $table->unsignedBigInteger('cancellation_id')->nullable()->after('is_pending');
            $table->foreign('cancellation_id')->references('id')->on('movement_cancellations')->onDelete('restrict');
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
            $table->unsignedBigInteger('cancel_user_id')->nullable()->after('user_id');
            $table->foreign('cancel_user_id')->references('id')->on('users')->onDelete('restrict');
            $table->datetime('cancel_date')->nullable()->after('cancel_user_id');
            $table->index('cancel_date');
            
            $table->dropColumn('is_pending');
            $table->dropForeign(['cancellation_id']);
            $table->dropColumn('cancellation_id');
        });
    }
}
