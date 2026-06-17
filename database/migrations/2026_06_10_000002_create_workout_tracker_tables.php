<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Workouts
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('difficulty'); // beginner, intermediate, advanced
            $table->string('video_url')->nullable();
            $table->string('type')->nullable(); // push_up, sit_up, squat, run, other
            $table->text('description')->nullable();
            $table->string('reps_label')->default('reps');
            $table->string('duration_label')->default('seconds');
            $table->timestamps();
        });

        // Workout Logs
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->integer('reps');
            $table->integer('duration'); // in seconds
            $table->integer('xp_earned');
            $table->timestamps();
        });

        // Missions (Daily targets setup)
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // push_up, sit_up, squat, run
            $table->integer('target');
            $table->integer('base_xp')->default(100);
            $table->timestamps();
        });

        // User Missions (Daily tracking per date)
        Schema::create('user_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mission_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->float('current_progress')->default(0);
            $table->integer('target_snapshot');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'mission_id', 'date']);
        });

        // Weekly Progress (Hell mode and completions)
        Schema::create('weekly_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('week_start'); // Monday of the week
            $table->integer('completed_days')->default(0);
            $table->boolean('hell_mode_ready')->default(false);
            $table->boolean('hell_mode_used')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'week_start']);
        });

        // Manual Logs
        Schema::create('manual_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('activity_name');
            $table->integer('duration'); // in minutes
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // Notifications log
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // morning, evening
            $table->text('message');
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('manual_logs');
        Schema::dropIfExists('weekly_progress');
        Schema::dropIfExists('user_missions');
        Schema::dropIfExists('missions');
        Schema::dropIfExists('workout_logs');
        Schema::dropIfExists('workouts');
    }
};
