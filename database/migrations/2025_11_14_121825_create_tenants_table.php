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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Dinas/OPD
            $table->string('code')->unique(); // Kode unik untuk dinas
            $table->string('email')->nullable(); // Email dinas
            $table->string('phone')->nullable(); // No telp dinas
            $table->text('address')->nullable(); // Alamat dinas
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
