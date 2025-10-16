<?php

namespace App\Console\Commands;

use App\Integrations\Cetesb;
use App\Models\Beach;
use Illuminate\Console\Command;

class UpdateRegistersFromCetesb extends Command
{
    protected $signature = 'app:update-registers-from-cetesb';
    
    protected $description = 'Update registers from CETESB';

    public function handle()
    {
        $integration = new Cetesb();
        $registers = $integration->getBeaches();

        $this->info('Processando ' . count($registers) . ' registros...');
        

        foreach ($registers as $register) {
            $attributes = $register['attributes'];

            $beach = Beach::where('code', $attributes['id_praia'])->first();

            if (!$beach) {
                continue;
            }

            $collectAtTimestamp = $attributes['data_amostra_final'];
            
            if ($collectAtTimestamp > 9999999999) {
                $collectAtTimestamp = $collectAtTimestamp / 1000;
            }
            
            $collectAt = \Carbon\Carbon::createFromTimestamp($collectAtTimestamp);

            $beach->registers()->firstOrCreate(
                ['collect_at' => $collectAt], 
                ['status' => mb_strtolower($attributes['classificacao_texto'])]
            );

            $this->info('Atualizado registro da praia: ' . $register['attributes']['id_praia']);
        }
    }
}
