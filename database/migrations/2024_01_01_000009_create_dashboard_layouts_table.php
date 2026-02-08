<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('voltpanel.dashboard.layouts_table', 'dashboard_layouts'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->json('widgets');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('voltpanel.dashboard.layouts_table', 'dashboard_layouts'));
    }
};
