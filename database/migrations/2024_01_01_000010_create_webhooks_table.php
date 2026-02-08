<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('voltpanel.webhooks.table_name', 'webhooks'), function (Blueprint $table) {
            $table->id();
            $table->string('event');
            $table->string('url');
            $table->json('headers')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('timeout')->default(10);
            $table->integer('successful_calls')->default(0);
            $table->integer('failed_calls')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('last_called_at')->nullable();
            $table->timestamps();

            $table->index(['event', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('voltpanel.webhooks.table_name', 'webhooks'));
    }
};
