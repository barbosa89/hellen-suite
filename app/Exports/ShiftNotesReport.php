<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ShiftNotesReport implements FromView, WithTitle
{
    /**
     * Notes attached to shift.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $notes;

    public function __construct(Collection $notes)
    {
        $this->notes = $notes;
    }

    public function view(): View
    {
        return view('app.shifts.exports.notes', [
            'notes' => $this->notes
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
