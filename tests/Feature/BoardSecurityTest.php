<?php

namespace Tests\Feature;

use App\Models\BoardMembership;
use App\Models\NoticeBoard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_member_cannot_view_private_board(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $board = NoticeBoard::create([
            'name' => 'Private Board',
            'description' => 'Secret stuff',
            'category' => 'General',
            'is_private' => true,
            'owner_id' => $owner->id,
        ]);

        BoardMembership::create([
            'user_id' => $owner->id,
            'notice_board_id' => $board->id,
            'role' => 'admin',
        ]);

        $this->actingAs($otherUser)
            ->get(route('boards.show', $board))
            ->assertForbidden();
    }

    public function test_member_can_view_private_board(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $board = NoticeBoard::create([
            'name' => 'Private Board',
            'description' => 'Secret stuff',
            'category' => 'General',
            'is_private' => true,
            'owner_id' => $owner->id,
        ]);

        BoardMembership::create([
            'user_id' => $owner->id,
            'notice_board_id' => $board->id,
            'role' => 'admin',
        ]);

        BoardMembership::create([
            'user_id' => $member->id,
            'notice_board_id' => $board->id,
            'role' => 'member',
        ]);

        $this->actingAs($member)
            ->get(route('boards.show', $board))
            ->assertOk();
    }
}
