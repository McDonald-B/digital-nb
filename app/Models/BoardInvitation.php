<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoardInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'notice_board_id',
        'invited_user_id',
        'invited_by',
        'status',
    ];

    public function board()
    {
        return $this->belongsTo(NoticeBoard::class, 'notice_board_id');
    }

    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
