<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('voltpanel.activity_log.table_name', 'activity_log'), function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->string('event')->nullable();
            $table->nullableMorphs('subject', 'subject');
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index('log_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('voltpanel.activity_log.table_name', 'activity_log'));
    }
};
