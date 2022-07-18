<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnteryModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_modes', function (Blueprint $table) {
            $table->id();
            $table->string('entry_mode_name', 100);
            $table->enum('crdr', ['C', 'D']);
            $table->unsignedInteger('entry_mode_id')->unique();
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
        Schema::dropIfExists('entery_modes');
    }
}
