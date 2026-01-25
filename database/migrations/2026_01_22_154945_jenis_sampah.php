<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
      Schema::create('kategori_sampah', function (Blueprint $table) {
    $table->id();
    $table->string('nama_sampah', 100)->unique();
    $table->string('kategori_sampah', 100)->nullable();
    $table->text('deskripsi')->nullable();
    $table->unsignedBigInteger('harga_satuan')->nullable(); // rupiah
    $table->string('jenis_satuan', 20)->nullable(); // kg, pcs, dll
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_sampah');
    }
};
