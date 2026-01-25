<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran_sampah_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('setoran_id')
                ->constrained('setoran_sampah')
                ->cascadeOnDelete();

            $table->foreignId('kategori_sampah_id')
                ->constrained('kategori_sampah')
                ->restrictOnDelete();

            $table->decimal('jumlah', 10, 2);
            $table->string('satuan', 50)->nullable(); // snapshot dari admin
            $table->unsignedBigInteger('harga_satuan')->nullable(); // snapshot dari admin
            $table->unsignedBigInteger('subtotal')->default(0); // jumlah * harga

            $table->timestamps();

            $table->index(['setoran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_sampah_detail');
    }
};
