<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penjualan_sampahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_sampah_id')->constrained('kategori_sampah')->onDelete('cascade');
            // Data Pembeli (Pengepul Besar)
            $table->string('pembeli');

            // Detail Transaksi
            $table->decimal('jumlah', 10, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->decimal('total_pendapatan', 15, 2);

            $table->date('tanggal_penjualan');
            $table->string('bukti_transaksi')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_sampahs');
    }
};
