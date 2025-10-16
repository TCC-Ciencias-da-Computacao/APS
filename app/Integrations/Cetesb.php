<?php

namespace App\Integrations;

use Illuminate\Support\Facades\Http;

class Cetesb
{
    public function getBeaches()
    {
        $response = Http::baseUrl(env('CETESB_BASE_URL'))
            ->withQueryParameters([
                'f' => 'json',
                'where' => '1=1',
                'outFields' => '*'
            ])
            ->get('/0/query');

        $beaches = $response->json();

        return $beaches['features'];
    }
}
