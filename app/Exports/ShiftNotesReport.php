<?php

namespace App\Exports;

use App\Models\Shift;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ShiftNotesReport implements FromView, WithTitle
{
    /**
     * Shift with notes.
     *
     * @var \App\Models\Shift
     */
    protected $shift;

    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    public function view(): View
    {
        return view('app.shifts.exports.notes', [
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
        return trans('notes.title');
    }
}
