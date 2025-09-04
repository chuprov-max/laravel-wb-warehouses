<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateIntervalToSearchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_requests', function (Blueprint $table) {
            $table->date('date_from')->nullable()->after('warehouses');
            $table->date('date_to')->nullable()->after('date_from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_requests', function (Blueprint $table) {
            $table->dropColumn(['date_from', 'date_to']);
        });
    }
}
