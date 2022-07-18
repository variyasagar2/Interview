<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommonFeeCollectionHeadwisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_fee_collection_headwises', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('module_id');
            $table->unsignedBigInteger('receipt_id');
            $table->unsignedInteger('head_id');
            $table->string('head_name', 120);
            $table->unsignedBigInteger('branch_id');
            $table->double('amount');
            $table->foreign('receipt_id')
                ->references('id')->on('common_fee_collections')
                ->onDelete('cascade');
            $table->foreign('module_id')
                ->references('module_id')->on('modules')
                ->onDelete('cascade');
            $table->foreign('branch_id')
                ->references('id')->on('branches')
                ->onDelete('cascade');
//            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('common_fee_collection_headwises');
    }
}
