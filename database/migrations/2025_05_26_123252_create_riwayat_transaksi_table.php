<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiwayatTransaksiTable extends Migration
{
    public function up()
    {
        Schema::create('riwayat_transaksi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('metode_pembayaran');
            $table->string('bukti_pembayaran')->nullable();
            $table->json('detail_keranjang');
            $table->timestamp('tanggal_transaksi');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('riwayat_transaksi');
    }
}
