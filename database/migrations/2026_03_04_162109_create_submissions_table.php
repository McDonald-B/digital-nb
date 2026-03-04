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
    Schema::create('submissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('notice_board_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('type'); // 'poster' or 'flyer'
        $table->string('title');
        $table->longText('content')->nullable(); // Rich text for flyers
        $table->string('file_path')->nullable(); // Image path for posters
        $table->string('status')->default('pending'); // pending, approved, flagged, rejected
        $table->timestamp('expires_at')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
