<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\Sharded;

class Beach extends Model
{
    use Sharded;
    protected $fillable = [
        'city',
        'state',
        'name',
        'code',
        'latitude',
        'longitude',
    ];

    public function registers(): HasMany
    {
        return $this->hasMany(Register::class, 'beache_id');
    }

    public function latestRegister(): HasOne
    {
        return $this->hasOne(Register::class, 'beache_id')
            ->latest('collect_at');
    }
}
