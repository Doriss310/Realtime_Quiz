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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('score')->default(0);
            $table->integer('streak')->default(0);  // Số câu trả lời đúng liên tiếp
            $table->json('answers')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
