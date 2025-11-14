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
        Schema::create('dokumen_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Tenant isolation
            $table->foreignId('pengajuan_kgb_id')->constrained('pengajuan_kgb')->onDelete('cascade'); // Referensi ke pengajuan
            $table->string('jenis_dokumen'); // sk_pangkat_terakhir, sk_kgb_terakhir, rekap_absensi, sk_unor, skp_2tahun
            $table->string('nama_file'); // Nama file asli
            $table->string('path_file'); // Path penyimpanan file
            $table->string('tipe_file')->default('pdf'); // Tipe file
            $table->bigInteger('ukuran_file'); // Ukuran file dalam bytes
            $table->string('status_verifikasi')->default('belum_diperiksa'); // belum_diperiksa, valid, tidak_valid, revisi
            $table->text('catatan_verifikasi')->nullable(); // Catatan dari verifikator
            $table->string('versi')->default('1.0'); // Versi dokumen (untuk revisi)
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->onDelete('set null'); // User verifikator
            $table->timestamp('tanggal_upload')->nullable(); // Tanggal upload
            $table->timestamp('tanggal_verifikasi')->nullable(); // Tanggal verifikasi
            $table->boolean('is_active')->default(true); // Status dokumen
            $table->timestamps();
            
            // Indexes
            $table->index(['pengajuan_kgb_id', 'jenis_dokumen']);
            $table->index(['status_verifikasi', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pengajuan');
    }
};
