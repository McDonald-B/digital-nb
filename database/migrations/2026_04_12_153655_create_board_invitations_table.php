<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notice_board_id')->constrained()->onDelete('cascade');
            $table->foreignId('invited_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, accepted, declined
            $table->timestamps();

            $table->unique(['notice_board_id', 'invited_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_invitations');
    }
};
