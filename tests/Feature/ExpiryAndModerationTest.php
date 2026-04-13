<?php

namespace Tests\Feature;

use App\Models\BoardMembership;
use App\Models\NoticeBoard;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpiryAndModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_flagged_submission(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $board = NoticeBoard::create([
            'name' => 'General Board',
            'description' => 'Board',
            'category' => 'General',
            'is_private' => false,
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
            ->post(route('submissions.store', $board), [
                'title' => 'This contains scam',
                'type' => 'flyer',
                'content' => 'bad content',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('submissions', [
            'notice_board_id' => $board->id,
            'user_id' => $member->id,
            'status' => 'flagged',
        ]);
    }

    public function test_remove_expired_command_deletes_expired_submission(): void
    {
        $user = User::factory()->create();

        $board = NoticeBoard::create([
            'name' => 'Jobs Board',
            'description' => 'Board',
            'category' => 'Jobs',
            'is_private' => false,
            'owner_id' => $user->id,
        ]);

        BoardMembership::create([
            'user_id' => $user->id,
            'notice_board_id' => $board->id,
            'role' => 'admin',
        ]);

        $submission = Submission::create([
            'notice_board_id' => $board->id,
            'user_id' => $user->id,
            'type' => 'flyer',
            'title' => 'Old post',
            'content' => 'Expired',
            'status' => 'approved',
            'expires_at' => now()->subDay(),
        ]);

        $this->artisan('submissions:remove-expired')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('submissions', [
            'id' => $submission->id,
        ]);
    }
}
