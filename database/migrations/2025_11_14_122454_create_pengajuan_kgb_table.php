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
        Schema::create('pengajuan_kgb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Tenant isolation
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade'); // Pegawai yang mengajukan
            $table->foreignId('user_pengaju_id')->nullable()->constrained('users')->onDelete('set null'); // User yang membuat pengajuan (jika admin/operator)
            $table->string('no_sk')->nullable(); // Nomor SK (jika sudah disetujui)
            $table->date('tanggal_sk')->nullable(); // Tanggal SK (jika sudah disetujui)
            $table->date('tmt_kgb_baru')->nullable(); // TMT KGB baru
            $table->string('status')->default('draft'); // draft, diajukan, verifikasi_dinas, verifikasi_kabupaten, disetujui, ditolak
            $table->text('catatan_verifikasi_dinas')->nullable(); // Catatan dari verifikator dinas
            $table->text('catatan_verifikasi_kabupaten')->nullable(); // Catatan dari verifikator kabupaten
            $table->integer('jumlah_revisi')->default(0); // Jumlah revisi
            $table->timestamp('tanggal_pengajuan')->nullable(); // Tanggal pengajuan
            $table->timestamp('tanggal_verifikasi_dinas')->nullable(); // Tanggal verifikasi dinas
            $table->timestamp('tanggal_verifikasi_kabupaten')->nullable(); // Tanggal verifikasi kabupaten
            $table->timestamp('tanggal_approve')->nullable(); // Tanggal approve final
            $table->timestamp('tanggal_selesai')->nullable(); // Tanggal selesai
            $table->string('jenis_pengajuan')->default('mandiri'); // mandiri (pegawai) atau admin (oleh admin dinas)
            $table->string('file_sk_path')->nullable(); // Path file SK jika sudah diupload
            $table->timestamps();
            
            // Indexes
            $table->index(['tenant_id', 'status']);
            $table->index(['pegawai_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_kgb');
    }
};
