<?php

namespace Database\Seeders;

use App\Models\BoardMembership;
use App\Models\NoticeBoard;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $member = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $board = NoticeBoard::create([
            'name' => 'Swansea Community Board',
            'description' => 'A sample board for testing posters and flyers.',
            'is_private' => false,
            'owner_id' => $admin->id,
        ]);

        BoardMembership::create([
            'user_id' => $admin->id,
            'notice_board_id' => $board->id,
            'role' => 'admin',
        ]);

        BoardMembership::create([
            'user_id' => $member->id,
            'notice_board_id' => $board->id,
            'role' => 'member',
        ]);

        Submission::create([
            'notice_board_id' => $board->id,
            'user_id' => $member->id,
            'type' => 'flyer',
            'title' => 'Welcome to the board',
            'content' => 'This is an approved seeded flyer so you can test the board page immediately.',
            'status' => 'approved',
            'expires_at' => now()->addWeek(),
        ]);
    }
}

