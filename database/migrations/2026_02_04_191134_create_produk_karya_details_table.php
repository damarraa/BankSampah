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
        Schema::create('produk_karya_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_karya_id')->constrained('produk_karyas')->onDelete('cascade');
            $table->foreignId('kategori_sampah_id')->constrained('kategori_sampah');
            $table->decimal('jumlah_pakai', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_karya_details');
    }
};
