<?php

namespace App\Http\Controllers;

use App\Http\Resources\BeachCollection;
use App\Models\Beach;
use Illuminate\Http\Request;

class BeachController extends Controller
{
    public function index(Request $request)
    {
        $beaches = Beach::with('latestRegister')
            ->when($request->input('name'), fn ($query) => $query->where('name', 'like', '%' . mb_strtolower($request->input('name')) . '%'))
            ->when($request->input('city'), fn ($query) => $query->where('city', 'like', '%' . mb_strtolower($request->input('city')) . '%'))
            ->when($request->input('status'), fn ($query) => $query->whereHas('latestRegister', function ($query) use ($request) {
                $query->where('status', mb_strtolower($request->input('status')));
            }))
            ->paginate(
                perPage: min($request->input('limit', 20), 100)
            );

        return new BeachCollection($beaches);
    }

    public function show()
    {
        //
    }
}
