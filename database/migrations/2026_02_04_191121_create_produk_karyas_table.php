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
        Schema::create('produk_karyas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_karya');
            $table->string('pembeli')->nullable();
            $table->decimal('harga_jual', 15, 2);
            $table->date('tanggal_dibuat');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_karyas');
    }
};
