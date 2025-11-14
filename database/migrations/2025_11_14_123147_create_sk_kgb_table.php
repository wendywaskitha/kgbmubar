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
        Schema::create('sk_kgb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Tenant isolation
            $table->foreignId('pengajuan_kgb_id')->constrained('pengajuan_kgb')->onDelete('cascade'); // Referensi ke pengajuan
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade'); // Pegawai terkait
            $table->string('no_sk')->unique(); // Nomor SK
            $table->date('tanggal_sk'); // Tanggal SK
            $table->string('file_path'); // Path file SK
            $table->string('file_name'); // Nama file asli
            $table->string('jenis_file')->default('scan'); // scan atau auto-generated
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null'); // User yang generate
            $table->timestamp('tanggal_upload')->nullable(); // Tanggal upload
            $table->boolean('is_active')->default(true); // Status dokumen
            $table->timestamps();
            
            // Indexes
            $table->index(['no_sk']);
            $table->index(['pegawai_id', 'tanggal_sk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sk_kgb');
    }
};
