<?php

namespace App\Console\Commands;

use App\Integrations\Cetesb;
use App\Models\Beach;
use Illuminate\Console\Command;

class RetrieveBeachesFromCetesb extends Command
{
    protected $signature = 'app:retrieve-beaches-from-cetesb';

    protected $description = 'Retrieve beaches from CETESB';

    public function handle()
    {
        $integration = new Cetesb();
        $beaches = $integration->getBeaches();

        $this->info('Processando ' . count($beaches) . ' praias...');

        foreach ($beaches as $beach) {
            $attributes = $beach['attributes'];

            $latitude = $beach['geometry']['y'];
            $longitude = $beach['geometry']['x'];

            Beach::updateOrCreate(
                ['code' => $attributes['id_praia']],
                [
                    'city' => mb_strtolower($attributes['municipio']),
                    'state' => 'sp',
                    'name' => mb_strtolower('praia ' . $attributes['praia']),
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]
            );
        }
    }
}
