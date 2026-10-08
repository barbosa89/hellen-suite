<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\JurisdictionSubdivision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JurisdictionLocalityController extends Controller
{
    public function __invoke(Request $request, Hotel $hotel): JsonResponse
    {
        $data = $request->validate([
            'country' => ['required', 'string', 'size:2'],
            'subdivision' => ['required', 'string', 'max:16'],
        ]);

        $subdivision = JurisdictionSubdivision::query()
            ->forCountry($data['country'])
            ->active()
            ->where('code', $data['subdivision'])
            ->first();

        if ($subdivision === null) {
            return response()->json(['localities' => []]);
        }

        return response()->json([
            'localities' => $subdivision->localities()
                ->active()
                ->orderBy('name')
                ->get(['code', 'name', 'type']),
        ]);
    }
}
