<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('service_masuk', function (Blueprint $table) {
            $table->string('status')->default('Menunggu')->change();
        });
    }

    public function down()
    {
        Schema::table('service_masuk', function (Blueprint $table) {
            $table->enum('status', ['online', 'service'])->default('online')->change();
        });
    }
};