<?php

namespace App\Exports;

use App\Models\Prop;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PropReport implements FromView
{
    /**
     * The Prop instance.
     *
     * @var \App\Models\Prop
     */
    protected $prop;

    public function __construct(Prop $prop)
    {
        $this->prop = $prop;
    }

    public function view(): View
    {
        return view('app.props.exports.prop', [
            'prop' => $this->prop
        ]);
    }
}
