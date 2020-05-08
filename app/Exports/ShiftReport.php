<?php

namespace App\Exports;

use App\Welkome\Shift;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ShiftReport implements WithMultipleSheets
{
    use Exportable;

    /**
     * The shift to export.
     *
     * @var \App\Welkome\Shift
     */
    protected Shift $shift;

    /**
     * The Hotel Rooms.
     *
     * @var \Illuminate\Support\Collection
     */
    protected Collection $rooms;

    /**
     * Construct function
     *
     * @param Shift $shift
     * @param Collection $rooms
     */
    public function __construct(Shift $shift, Collection $rooms)
    {
        $this->shift = $shift;
        $this->rooms = $rooms;
    }

    /**
     * Report sheets
     *
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new ShiftVouchersReport($this->shift);
        $sheets[] = new ShiftRoomsReport($this->rooms);
        $sheets[] = new ShiftNotesReport($this->shift->notes);


        return $sheets;
    }
}
