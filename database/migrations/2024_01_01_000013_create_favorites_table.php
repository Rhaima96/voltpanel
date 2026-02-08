<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('voltpanel.favorites.table_name', 'favorites'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('favoritable');
            $table->timestamps();

            $table->unique(['user_id', 'favoritable_id', 'favoritable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('voltpanel.favorites.table_name', 'favorites'));
    }
};
