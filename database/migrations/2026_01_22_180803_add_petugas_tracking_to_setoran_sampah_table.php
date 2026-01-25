<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setoran_sampah', function (Blueprint $table) {
            // petugas yang mengerjakan jemput
            $table->foreignId('petugas_id')->nullable()->after('user_id')
                ->constrained('users')->nullOnDelete();

            // lokasi petugas realtime
            $table->decimal('petugas_latitude', 10, 7)->nullable()->after('longitude');
            $table->decimal('petugas_longitude', 10, 7)->nullable()->after('petugas_latitude');
            $table->dateTime('petugas_last_seen')->nullable()->after('petugas_longitude');

            $table->index(['petugas_id']);
        });
    }

    public function down(): void
    {
        Schema::table('setoran_sampah', function (Blueprint $table) {
            $table->dropIndex(['petugas_id']);
            $table->dropForeign(['petugas_id']);
            $table->dropColumn(['petugas_id','petugas_latitude','petugas_longitude','petugas_last_seen']);
        });
    }
};
