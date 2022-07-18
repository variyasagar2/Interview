<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialTransDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_trans_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('financial_trans_id');
            $table->unsignedInteger('module_id');
            $table->double('amount');
            $table->unsignedInteger('head_id');
            $table->enum('crdr', ['C', 'D']);
            $table->unsignedBigInteger('branch_id');
            $table->string('head_name', 120);
            $table->foreign('financial_trans_id')
                ->references('id')->on('financial_trans')
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
        Schema::dropIfExists('financial_trans_details');
    }
}
