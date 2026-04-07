<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardMembership extends Model
{
    protected $fillable = [
        'user_id',
        'notice_board_id',
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function board()
    {
        return $this->belongsTo(NoticeBoard::class, 'notice_board_id');
    }
}
