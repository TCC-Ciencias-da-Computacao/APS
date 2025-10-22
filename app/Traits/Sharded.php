<?php

namespace App\Traits;

use App\Services\ShardManager;

trait Sharded
{
    /**
     * Determine the connection name for the model dynamically.
     * The model using this trait must implement getShardKeyValue(): string|int
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        if (method_exists($this, 'getShardKeyValue')) {
            $key = $this->getShardKeyValue();
            return ShardManager::connectionFor($key);
        }

        return parent::getConnectionName();
    }
}
