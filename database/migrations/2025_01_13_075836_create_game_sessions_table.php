<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 6)->unique(); // Mã phòng 6 ký tự
            $table->boolean('timer_enabled')->default(true);
            $table->integer('timer_limit')->nullable();
            $table->foreignId('quiz_id')->constrained();
            $table->foreignId('host_id')->constrained('users');
            $table->enum('status', ['waiting', 'playing', 'finished'])->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
