<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'notice_board_id',
        'user_id',
        'type',
        'title',
        'content',
        'file_path',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function board()
    {
        return $this->belongsTo(NoticeBoard::class, 'notice_board_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
