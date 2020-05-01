<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CompaniesReport implements FromView
{
    /**
     * The Company collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $companies;

    public function __construct(Collection $companies)
    {
        $this->companies = $companies;
    }

    public function view(): View
    {
        return view('app.companies.exports.companies', [
            'companies' => $this->companies
        ]);
    }
}
