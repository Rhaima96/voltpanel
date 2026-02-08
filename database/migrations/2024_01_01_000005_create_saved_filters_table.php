<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('voltpanel.saved_filters.table_name', 'saved_filters'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('resource');
            $table->string('name');
            $table->json('filters');
            $table->boolean('is_public')->default(false);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'resource']);
            $table->index('is_public');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('voltpanel.saved_filters.table_name', 'saved_filters'));
    }
};
