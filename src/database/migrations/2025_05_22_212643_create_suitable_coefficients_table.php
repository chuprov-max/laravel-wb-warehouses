<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuitableCoefficientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suitable_coefficients', function (Blueprint $table) {
            $table->id();
            $table->integer('warehouse_id');
            $table->integer('coefficient');
            $table->boolean('allow_unload');
            $table->integer('box_type_id');
            $table->date('accept_date');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suitable_coefficients');
    }
}
