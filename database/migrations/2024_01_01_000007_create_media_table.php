<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('voltpanel.media.table_name', 'media'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('path');
            $table->string('disk')->default('public');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('collection')->default('default');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'collection']);
            $table->index('mime_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('voltpanel.media.table_name', 'media'));
    }
};
