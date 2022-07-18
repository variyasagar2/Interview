<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_trans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('module_id');
            $table->string('tran_id',30)->nullable();
            $table->string('adm_no', 30);
            $table->double('amount')->default(0);
            $table->enum('crdr', ['C', 'D'])->default('C');
            $table->date('tran_date');
            $table->string('acad_year', 10);
            $table->unsignedInteger('entry_mode_id');
            $table->string('voucher_no', 12);
            $table->unsignedBigInteger('branch_id');
            $table->enum('type_of_consession', ['1', '2']);

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
        Schema::dropIfExists('financial_trans');
    }
}
