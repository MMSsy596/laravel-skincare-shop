<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'type',
        'message',
        'mode',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create session ID for guest users
     */
    public static function getSessionId()
    {
        if (!session()->has('chat_session_id')) {
            session(['chat_session_id' => uniqid('chat_', true)]);
        }
        return session('chat_session_id');
    }
}
