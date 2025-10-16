<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Register extends Model
{
    protected $fillable = [
        'beache_id',
        'collect_at',
        'status',
    ];

    protected $casts = [
        'collect_at' => 'datetime',
    ];

    public function beach(): BelongsTo
    {
        return $this->belongsTo(Beach::class, 'beache_id');
    }
}
