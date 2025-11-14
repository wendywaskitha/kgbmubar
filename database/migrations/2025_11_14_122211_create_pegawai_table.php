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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Tenant isolation
            $table->string('nip')->unique(); // Nomor Induk Pegawai
            $table->string('name'); // Nama lengkap
            $table->string('nrk')->nullable(); // Nomor Register Kepegawaian
            $table->string('pangkat_golongan')->nullable(); // Pangkat dan golongan
            $table->string('jabatan')->nullable(); // Jabatan
            $table->string('unit_kerja')->nullable(); // Unit kerja
            $table->date('tmt_pangkat_terakhir')->nullable(); // TMT pangkat terakhir
            $table->date('tmt_kgb_terakhir')->nullable(); // TMT KGB terakhir
            $table->date('tmt_kgb_berikutnya')->nullable(); // TMT KGB berikutnya (untuk perhitungan otomatis)
            $table->string('jenis_kelamin')->nullable(); // Jenis kelamin
            $table->date('tanggal_lahir')->nullable(); // Tanggal lahir
            $table->string('tempat_lahir')->nullable(); // Tempat lahir
            $table->string('status_kepegawaian')->nullable(); // PNS/PPPK
            $table->string('email')->nullable(); // Email pribadi
            $table->string('phone')->nullable(); // No HP
            $table->boolean('is_active')->default(true); // Status aktif
            $table->timestamps();
            
            $table->index(['tenant_id', 'nip']); // Index untuk tenant dan NIP
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
