<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;

class GuestsReport implements FromView
{
    /**
     * The Company collection.
     *
     * @var \App\Models\Company
     */
    protected $guests;

    public function __construct(Collection $guests)
    {
        $this->guests = $guests;
    }

    public function view(): View
    {
        return view('app.guests.exports.guests', [
            'guests' => $this->guests,
        ]);
    }
}
