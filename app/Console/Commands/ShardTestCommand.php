<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ShardManager;

class ShardTestCommand extends Command
{
    protected $signature = 'shard:test {key}';
    protected $description = 'Print the shard connection for a given key';

    public function handle()
    {
        $key = $this->argument('key');
        $conn = ShardManager::connectionFor($key);
        $this->info($conn);
        return 0;
    }
}
