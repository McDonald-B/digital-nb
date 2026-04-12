<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NoticeBoard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'is_private',
        'owner_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->hasMany(BoardMembership::class, 'notice_board_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'notice_board_id');
    }

    public function invitations()
    {
        return $this->hasMany(BoardInvitation::class, 'notice_board_id');
    }
}
