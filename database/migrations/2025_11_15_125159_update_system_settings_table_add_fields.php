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
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('group')->default('general')->after('type');
            $table->boolean('is_public')->default(false)->after('is_global');
            $table->integer('sort_order')->default(0)->after('is_public');

            // Add foreign key constraint for tenant_id if it doesn't exist
            if (!Schema::hasColumn('system_settings', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['group', 'is_public', 'sort_order']);
        });
    }
};
