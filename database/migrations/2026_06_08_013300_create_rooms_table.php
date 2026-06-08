<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_code')->unique();
            $table->string('name');
            $table->string('password')->nullable();
            $table->foreignId('host_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('guest_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->boolean('host_ready')->default(false);
            $table->boolean('guest_ready')->default(false);
            $table->string('status')->default('waiting'); // waiting, playing, finished
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('loser_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
