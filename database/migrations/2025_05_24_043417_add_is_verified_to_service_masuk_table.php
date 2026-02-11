<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsVerifiedToServiceMasukTable extends Migration
{
    public function up()
    {
        Schema::table('service_masuk', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('service_masuk', function (Blueprint $table) {
            $table->dropColumn('is_verified');
        });
    }
}