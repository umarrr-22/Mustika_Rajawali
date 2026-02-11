<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefilMasukTable extends Migration
{
    public function up()
    {
        Schema::create('refil_masuk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nomor_urut')->unique()->nullable();
            $table->date('tanggal_masuk');
            $table->string('nama_pelanggan');
            $table->string('no_telepon');
            $table->string('jenis_layanan');
            $table->string('jenis_kartrid');
            $table->text('alamat');
            $table->text('kerusakan');
            $table->string('foto_kartrid')->nullable();
            $table->string('diambil_oleh')->nullable();
            $table->string('status')->default('masuk');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('ditangani_oleh')->nullable()->constrained('users');
            $table->text('sparepart')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('refil_masuk');
    }
}