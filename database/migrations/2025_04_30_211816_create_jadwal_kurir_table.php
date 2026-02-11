<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jadwal_kurir', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nomor_urut')->unique()->nullable();
            $table->date('tanggal');
            $table->string('daerah');
            $table->string('lokasi_tujuan');
            $table->text('alamat');
            $table->text('keperluan');
            $table->enum('status', ['draft', 'dikirim', 'selesai'])->default('draft');
            $table->datetime('tanggal_kirim')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('kurir_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_duplicated')->default(false);
            $table->timestamps();
            
            $table->index('nomor_urut');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal_kurir');
    }
};