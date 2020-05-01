<?php

namespace App\Exports;

use App\Welkome\Shift;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ShiftVouchersReport implements FromView, WithTitle
{
    /**
     * The shift to export.
     *
     * @var \App\Welkome\Shift
     */
    protected $shift;

    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    public function view(): View
    {
        return view('app.shifts.exports.shift', [
            'shift' => $this->shift
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
