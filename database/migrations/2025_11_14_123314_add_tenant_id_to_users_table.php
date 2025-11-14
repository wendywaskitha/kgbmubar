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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('set null'); // For tenant-specific users
            $table->string('role')->default('pegawai'); // Role: super_admin, verifikator_kabupaten, admin_dinas, verifikator_dinas, operator_dinas, pegawai
            $table->boolean('is_active')->default(true); // Status aktif user
            
            // Index for tenant_id to improve performance
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id']);
            $table->dropColumn(['tenant_id', 'role', 'is_active']);
        });
    }
};
