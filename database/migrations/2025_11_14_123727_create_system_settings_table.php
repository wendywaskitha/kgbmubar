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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Kunci setting (misal: 'kgb_eligibility_period', 'notification_enabled', etc)
            $table->text('value'); // Nilai setting
            $table->string('type')->default('string'); // Tipe data: string, integer, boolean, json
            $table->text('description')->nullable(); // Deskripsi setting
            $table->boolean('is_global')->default(true); // true=setting global, false=setting per tenant
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade'); // Jika setting per tenant
            $table->timestamps();
            
            // Indexes
            $table->index(['key']);
            $table->index(['is_global', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
