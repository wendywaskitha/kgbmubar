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
        Schema::create('verifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Tenant isolation
            $table->foreignId('pengajuan_kgb_id')->constrained('pengajuan_kgb')->onDelete('cascade'); // Referensi ke pengajuan
            $table->string('jenis_verifikasi'); // dinas atau kabupaten
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->onDelete('set null'); // User verifikator
            $table->string('status'); // pending, lolos, revisi, ditolak
            $table->text('catatan')->nullable(); // Catatan verifikasi
            $table->json('hasil_verifikasi_dokumen')->nullable(); // Hasil verifikasi per dokumen
            $table->timestamp('tanggal_verifikasi')->nullable(); // Tanggal verifikasi
            $table->timestamps();
            
            // Indexes
            $table->index(['pengajuan_kgb_id', 'jenis_verifikasi']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi');
    }
};
