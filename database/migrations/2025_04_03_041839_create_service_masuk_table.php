<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_masuk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nomor_urut')->unique()->nullable(); // Kolom baru untuk nomor urut stabil
            $table->date('tanggal_masuk');
            $table->string('nama_pelanggan');
            $table->string('no_telepon');
            $table->text('alamat');
            $table->string('jenis_layanan');
            $table->string('jenis_barang');
            $table->text('kerusakan');
            $table->text('sparepart_diganti')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto_barang')->nullable();
            $table->string('diambil_oleh')->nullable();
            $table->string('status')->default('Menunggu');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('teknisi_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->text('alasan_penghapusan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_masuk');
    }
};