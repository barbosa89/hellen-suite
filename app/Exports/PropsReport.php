<?php

namespace App\Exports;

use App\Welkome\Prop;
use Maatwebsite\Excel\Concerns\FromCollection;

class PropsReport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Prop::all();
    }
}
