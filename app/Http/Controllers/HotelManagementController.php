<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Hotels\SummarizeHotelDashboard;
use App\Models\Hotel;
use App\Settings\GeneralSettings;
use Inertia\Response;

class HotelManagementController extends Controller
{
    public function index(Hotel $hotel, GeneralSettings $settings, SummarizeHotelDashboard $summarizeHotelDashboard): Response
    {
        return inertia('Hotels/Management/Index', [
            'hotel' => $hotel,
            'dashboard' => $summarizeHotelDashboard->execute($hotel, $settings->currency),
        ]);
    }
}
