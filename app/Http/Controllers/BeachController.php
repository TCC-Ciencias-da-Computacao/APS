<?php

namespace App\Http\Controllers;

use App\Http\Resources\BeachCollection;
use App\Models\Beach;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BeachController extends Controller
{
    public function index(Request $request)
    {
        $shardCount = (int) config('database.shard_count', env('DB_SHARD_COUNT', 0));
        $perPage = min($request->input('limit', 20), 100);
        $currentPage = $request->input('page', 1);
        
        // Coletar beaches de todos os shards
        $allBeaches = collect();
        
        for ($i = 0; $i < $shardCount; $i++) {
            $connection = "sqlite_shard_{$i}";
            
            $shardBeaches = Beach::on($connection)
                ->with('latestRegister')
                ->when($request->input('name'), fn ($query) => $query->where('name', 'like', '%' . mb_strtolower($request->input('name')) . '%'))
                ->when($request->input('city'), fn ($query) => $query->where('city', 'like', '%' . mb_strtolower($request->input('city')) . '%'))
                ->when($request->input('status'), fn ($query) => $query->whereHas('latestRegister', function ($subQuery) use ($request) {
                    $subQuery->where('status', mb_strtolower($request->input('status')));
                }))
                ->get();
            
            $allBeaches = $allBeaches->merge($shardBeaches);
        }
        
        // Ordenar por nome (ou outro critério)
        $allBeaches = $allBeaches->sortBy('name')->values();
        
        // Paginar manualmente
        $total = $allBeaches->count();
        $items = $allBeaches->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return new BeachCollection($paginator);
    }

    public function show()
    {
        //
    }
}
