<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('voltpanel.multi_tenancy.tenants_table', 'tenants'), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subdomain')->nullable()->unique();
            $table->string('domain')->nullable()->unique();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create(config('voltpanel.multi_tenancy.tenant_user_table', 'tenant_user'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained(config('voltpanel.multi_tenancy.tenants_table', 'tenants'))->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->nullable(); // tenant-specific role
            $table->timestamps();

            $table->unique(['tenant_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('voltpanel.multi_tenancy.tenant_user_table', 'tenant_user'));
        Schema::dropIfExists(config('voltpanel.multi_tenancy.tenants_table', 'tenants'));
    }
};
