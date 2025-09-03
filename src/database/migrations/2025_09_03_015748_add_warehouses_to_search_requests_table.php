<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehousesToSearchRequestsTable extends Migration
{
    public function up(): void
    {
        Schema::table('search_requests', function (Blueprint $table) {
            $table->json('warehouses')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('search_requests', function (Blueprint $table) {
            $table->dropColumn('warehouses');
        });
    }
}
