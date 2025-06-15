<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSearchRequestIdToSuitableCoefficientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suitable_coefficients', function (Blueprint $table) {
            $table->integer('search_request_id')->nullable()->after('warehouse_id');
        });


        DB::table('suitable_coefficients')->update(['search_request_id' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suitable_coefficients', function (Blueprint $table) {
            $table->dropColumn('search_request_id');
        });
    }
}
