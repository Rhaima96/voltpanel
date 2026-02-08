<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('voltpanel.table_preferences.table_name', 'user_table_preferences'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('table_identifier');
            $table->json('visible_columns')->nullable();
            $table->json('hidden_columns')->nullable();
            $table->json('column_order')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'table_identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('voltpanel.table_preferences.table_name', 'user_table_preferences'));
    }
};
