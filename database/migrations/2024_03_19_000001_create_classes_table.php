<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['yoga', 'cardio', 'strength', 'hiit']);
            $table->integer('max_capacity');
            $table->time('start_time');
            $table->time('end_time');
            $table->json('days_of_week'); // Store days as JSON array [1,2,3] where 1=Monday
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_classes');
    }
}; 