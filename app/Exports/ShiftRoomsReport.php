<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ShiftRoomsReport implements FromView, WithTitle
{
    /**
     * All hotel rooms.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $rooms;

    public function __construct(Collection $rooms)
    {
        $this->rooms = $rooms;
    }

    public function view(): View
    {
        return view('app.shifts.exports.rooms', [
            'rooms' => $this->rooms
        ]);
    }

    /**
     * Sheet title
     *
     * @return string
     */
    public function title(): string
    {
        return trans('rooms.title');
    }
}
