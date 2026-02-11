<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerifikasiToRefilMasukTable extends Migration
{
    public function up()
    {
        Schema::table('refil_masuk', function (Blueprint $table) {
            $table->boolean('verifikasi')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('refil_masuk', function (Blueprint $table) {
            $table->dropColumn('verifikasi');
        });
    }
}