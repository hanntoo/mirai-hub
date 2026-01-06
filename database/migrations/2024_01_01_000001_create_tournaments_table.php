<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('game_type');
            $table->string('banner_path')->nullable();
            $table->dateTime('event_date');
            $table->decimal('fee', 12, 2)->default(0);
            $table->integer('max_slots')->default(32);
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->jsonb('form_schema')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
