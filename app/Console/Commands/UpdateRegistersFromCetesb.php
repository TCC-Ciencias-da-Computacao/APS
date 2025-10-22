<?php

namespace App\Console\Commands;

use App\Integrations\Cetesb;
use App\Models\Beach;
use App\Services\ShardManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateRegistersFromCetesb extends Command
{
    protected $signature = 'app:update-registers-from-cetesb';
    
    protected $description = 'Update registers from CETESB';

    public function handle()
    {
        $integration = new Cetesb();
        $registers = $integration->getBeaches();

        $this->info('Processando ' . count($registers) . ' registros...');
        

        forEach ($registers as $register) {
            $attributes = $register['attributes'] ?? null;

            if (empty($attributes) || empty($attributes['id_praia'])) {
                $this->warn('Registro pulado: id_praia ausente');
                continue;
            }

            $beachCode = $attributes['id_praia'];

            // Decide shard based on beach code (id_praia) and perform operation on that connection
            if (empty($attributes['id_praia'])) {
                $this->warn('Praia pulada: id_praia ausente');
                dd('SEM ID_PRAIA');
            }

            // Primeiro, tente localizar a beach no shard esperado
            $connection = ShardManager::connectionFor($beachCode);
            $beach = Beach::on($connection)->where('code', $beachCode)->first();

            // Fallback: se não encontrado, procure em todos os shards configurados
            if (!$beach) {
                $shardCount = (int) config('database.shard_count', env('DB_SHARD_COUNT', 0));
                for ($i = 0; $i < $shardCount; $i++) {
                    $conn = preg_replace('/_shard_\d+$/', "_shard_{$i}", $connection);
                    $beach = Beach::on($conn)->where('code', $beachCode)->first();
                    if ($beach) {
                        $connection = $conn;
                        break;
                    }
                }
            }

            if (!$beach) {
                $this->warn('Praia não encontrada para code: ' . $beachCode);
                continue;
            }

            $collectAtTimestamp = $attributes['data_amostra_final'] ?? null;

            if (is_null($collectAtTimestamp)) {
                $this->warn('Timestamp ausente para praia: ' . $beachCode);
                continue;
            }

            if ($collectAtTimestamp > 9999999999) {
                $collectAtTimestamp = $collectAtTimestamp / 1000;
            }

            $collectAt = \Carbon\Carbon::createFromTimestamp((int) $collectAtTimestamp);

            // Criar o registro no mesmo shard da praia usando Query Builder
            $existing = DB::connection($connection)
                ->table('registers')
                ->where('beache_id', $beach->id)
                ->where('collect_at', $collectAt)
                ->first();

            if ($existing) {
                DB::connection($connection)
                    ->table('registers')
                    ->where('id', $existing->id)
                    ->update([
                        'status' => mb_strtolower($attributes['classificacao_texto'] ?? ''),
                        'updated_at' => now()
                    ]);
            } else {
                DB::connection($connection)
                    ->table('registers')
                    ->insert([
                        'beache_id' => $beach->id,
                        'collect_at' => $collectAt,
                        'status' => mb_strtolower($attributes['classificacao_texto'] ?? ''),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
            }

            $this->info('Atualizado registro da praia: ' . $beachCode . ' (shard: ' . $connection . ', beach_id: ' . $beach->id . ')');
        }
    }
}
