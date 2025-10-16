<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BeachCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'count' => $this->collection->count(),
            'next' => $this->nextPageUrl($request),
            'previous' => $this->previousPageUrl($request),
            'results' => BeachResource::collection($this->collection)
        ];
    }

    public function paginationInformation($request, $paginated, $default)
    {
        unset($default['links']);
        unset($default['meta']);

        return $default;
    }
}
