<?php

namespace Tests\Feature;

use App\Models\BoardMembership;
use App\Models\NoticeBoard;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_board_admin_can_access_admin_page(): void
    {
        $owner = User::factory()->create();
        $boardAdmin = User::factory()->create();
        $author = User::factory()->create();

        $board = NoticeBoard::create([
            'name' => 'Sports Board',
            'description' => 'Board',
            'category' => 'Sports',
            'is_private' => false,
            'owner_id' => $owner->id,
        ]);

        BoardMembership::create([
            'user_id' => $owner->id,
            'notice_board_id' => $board->id,
            'role' => 'admin',
        ]);

        BoardMembership::create([
            'user_id' => $boardAdmin->id,
            'notice_board_id' => $board->id,
            'role' => 'admin',
        ]);

        Submission::create([
            'notice_board_id' => $board->id,
            'user_id' => $author->id,
            'type' => 'flyer',
            'title' => 'Pending post',
            'content' => 'Review me',
            'status' => 'pending',
            'expires_at' => now()->addWeek(),
        ]);

        $this->actingAs($boardAdmin)
            ->get(route('admin.index'))
            ->assertOk();
    }

    public function test_normal_member_cannot_access_admin_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.index'))
            ->assertForbidden();
    }

    public function test_board_admin_can_approve_submission_for_their_board(): void
    {
        $owner = User::factory()->create();
        $boardAdmin = User::factory()->create();
        $author = User::factory()->create();

        $board = NoticeBoard::create([
            'name' => 'University Board',
            'description' => 'Board',
            'category' => 'University',
            'is_private' => false,
            'owner_id' => $owner->id,
        ]);

        BoardMembership::create([
            'user_id' => $owner->id,
            'notice_board_id' => $board->id,
            'role' => 'admin',
        ]);

        BoardMembership::create([
            'user_id' => $boardAdmin->id,
            'notice_board_id' => $board->id,
            'role' => 'admin',
        ]);

        $submission = Submission::create([
            'notice_board_id' => $board->id,
            'user_id' => $author->id,
            'type' => 'flyer',
            'title' => 'Pending post',
            'content' => 'Review me',
            'status' => 'pending',
            'expires_at' => now()->addWeek(),
        ]);

        $this->actingAs($boardAdmin)
            ->patch(route('admin.approve', $submission))
            ->assertRedirect();

        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id,
            'status' => 'approved',
        ]);
    }
}
