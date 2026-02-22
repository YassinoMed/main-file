<?php

namespace App\Models\Collaboration;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'commentable_type',
        'commentable_id',
        'content',
        'parent_id',
        'mentions',
    ];

    protected $casts = [
        'mentions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function commentable()
    {
        return $this->morphTo('commentable');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($comment) {
            $comment->notifyMentions();
        });
    }

    protected function notifyMentions()
    {
        $mentions = $this->mentions ?? [];

        foreach ($mentions as $userId) {
            \App\Models\User::find($userId)?->notify(new \App\Notifications\MentionNotification($this));
        }
    }

    public static function parseMentions($content)
    {
        preg_match_all('/@(\w+)/', $content, $matches);

        $mentions = [];
        foreach ($matches[1] as $username) {
            $user = \App\Models\User::where('username', $username)->first();
            if ($user) {
                $mentions[] = $user->id;
            }
        }

        return $mentions;
    }
}
