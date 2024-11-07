<?php

namespace App\Exports;

use App\Models\Shift;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ShiftReport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        protected Shift $shift,
        protected Collection $rooms
    )
    {
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
