<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hotel;

class HotelManagementController extends Controller
{
    public function index(Hotel $hotel)
    {
        return inertia('Hotels/Management/Index', [
            'hotel' => $hotel,
        ]);
    }
}
