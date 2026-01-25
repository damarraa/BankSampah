<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Table master kategori
        Schema::create('master_kategori_sampah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 2) Tambah foreign key ke tabel kategori_sampah
        Schema::table('kategori_sampah', function (Blueprint $table) {
            // simpan kategori master sebagai FK (nullable supaya aman untuk data lama)
            $table->foreignId('master_kategori_id')
                ->nullable()
                ->after('nama_sampah')
                ->constrained('master_kategori_sampah')
                ->nullOnDelete();

            // kolom lama kategori_sampah boleh kamu pertahankan dulu (opsional),
            // kalau nanti sudah aman, bisa dibuat migration terpisah untuk drop.
        });
    }

    public function down(): void
    {
        Schema::table('kategori_sampah', function (Blueprint $table) {
            $table->dropForeign(['master_kategori_id']);
            $table->dropColumn('master_kategori_id');
        });

        Schema::dropIfExists('master_kategori_sampah');
    }
};
