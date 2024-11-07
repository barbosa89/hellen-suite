<?php

namespace App\Exports;

use App\Models\Shift;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ShiftReport implements WithMultipleSheets
{
    use Exportable;

    /**
     * The shift to export.
     */
    protected Shift $shift;

    /**
     * The Hotel Rooms.
     */
    protected Collection $rooms;

    /**
     * Construct function
     */
    public function __construct(Shift $shift, Collection $rooms)
    {
        $this->shift = $shift;
        $this->rooms = $rooms;
    }

    /**
     * Report sheets
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new ShiftVouchersReport($this->shift);
        $sheets[] = new ShiftRoomsReport($this->rooms);
        $sheets[] = new ShiftNotesReport($this->shift);

        return $sheets;
    }
}
