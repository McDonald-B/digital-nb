<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticeBoard extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_private',
        'owner_id',
    ];

    protected $casts = [
        'is_private' => 'boolean',
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
}
