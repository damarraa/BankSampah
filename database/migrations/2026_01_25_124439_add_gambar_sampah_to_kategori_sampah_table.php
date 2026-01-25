<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategori_sampah', function (Blueprint $table) {
            $table->string('gambar_sampah')->nullable()->after('jenis_satuan');
        });
    }

    public function down(): void
    {
        Schema::table('kategori_sampah', function (Blueprint $table) {
            $table->dropColumn('gambar_sampah');
        });
    }
};
