<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommonFeeCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_fee_collections', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('module_id');
            $table->string('tran_id', 30)->nullable();
            $table->string('adm_no', 30);
            $table->string('roll_no', 50);
            $table->double('amount')->default(0);
            $table->unsignedBigInteger('branch_id');
            $table->string('acadamic_year', 12);
            $table->string('financial_year', 12);
            $table->string('display_receipt_no', 60)->nullable();
            $table->unsignedInteger('entry_mode_id');
            $table->date('paid_date');
            $table->enum('inactive', ['1', '2'])->default('1');

            $table->foreign('entry_mode_id')
                ->references('entry_mode_id')->on('entry_modes')
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
        Schema::dropIfExists('common_fee_collections');
    }
}
