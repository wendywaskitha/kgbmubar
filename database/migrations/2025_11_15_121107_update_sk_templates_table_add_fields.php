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
        Schema::table('sk_templates', function (Blueprint $table) {
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name');
            $table->string('kode_template');
            $table->text('content'); // HTML content with placeholders
            $table->string('jenis_pengajuan')->default('kenaikan_gaji_berkala'); // or 'kenaikan_pangkat'
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sk_templates', function (Blueprint $table) {
            $table->dropColumn([
                'tenant_id',
                'name',
                'kode_template',
                'content',
                'jenis_pengajuan',
                'is_active',
            ]);
        });
    }
};
