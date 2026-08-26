<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Stays\UpdateExpectedCheckOut;
use App\Http\Requests\Stays\UpdateStayExpectedCheckOutRequest;
use App\Models\Hotel;
use App\Models\Stay;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;

class UpdateStayExpectedCheckOutController extends Controller
{
    public function __invoke(UpdateStayExpectedCheckOutRequest $request, Hotel $hotel, Stay $stay, UpdateExpectedCheckOut $updateExpectedCheckOut): RedirectResponse
    {
        $updateExpectedCheckOut->execute($stay, CarbonImmutable::parse($request->validated('expected_check_out_on')));

        return back()->with('success', trans('stays.messages.expected_check_out_updated'));
    }
}
