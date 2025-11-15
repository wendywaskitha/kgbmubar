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
        Schema::table('pengajuan_kgb', function (Blueprint $table) {
            // Make pegawai_id nullable temporarily for development
            $table->foreignId('pegawai_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_kgb', function (Blueprint $table) {
            // Revert back to NOT NULL
            $table->foreignId('pegawai_id')->nullable(false)->change();
        });
    }
};
