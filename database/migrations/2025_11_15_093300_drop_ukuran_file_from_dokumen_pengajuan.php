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
        Schema::table('dokumen_pengajuan', function (Blueprint $table) {
            $table->dropColumn('ukuran_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_pengajuan', function (Blueprint $table) {
            $table->bigInteger('ukuran_file')->default(0); // Recovery if needed
        });
    }
};
