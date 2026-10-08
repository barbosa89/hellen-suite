<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Guests\LookupGuestRequest;
use App\Models\Hotel;
use Illuminate\Http\JsonResponse;

class GuestLookupController extends Controller
{
    public function __invoke(LookupGuestRequest $request, Hotel $hotel): JsonResponse
    {
        $search = $request->query('search', '');

        $guests = $hotel->guests()
            ->select(['id', 'identification_type_id', 'first_name', 'second_first_name', 'last_name', 'second_last_name', 'identification_number', 'birth_date', 'gender', 'nationality', 'residence_country', 'mobile', 'email'])
            ->with('identificationType:id,code')
            ->when(! blank($search), function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('identification_number', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(10)
            ->get();

        return response()->json(['guests' => $guests]);
    }
}
