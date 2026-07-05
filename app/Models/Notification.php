<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'order_id', 'channel', 'title', 'body', 'is_read', 'sent_at', 'created_at'];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
