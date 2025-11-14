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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable'); // This creates notifiable_type and notifiable_id
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade'); // Tenant isolation
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Index for tenant_id to improve performance with tenant isolation
            $table->index(['tenant_id', 'notifiable_type', 'notifiable_id']);
            $table->index(['tenant_id', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
