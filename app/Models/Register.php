<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Sharded;

class Register extends Model
{
    use Sharded;
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

    /**
     * Value used to decide shard for this register.
     * We'll use the beache_id (foreign key) so registers are colocated
     * with their beach's shard.
     *
     * @return int|null
     */
    public function getShardKeyValue()
    {
        return $this->beache_id ?? null;
    }
}
