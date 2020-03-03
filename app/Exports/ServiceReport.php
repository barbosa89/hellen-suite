<?php

namespace App\Exports;

use App\Welkome\Service;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ServiceReport implements FromView
{
    /**
     * The Service instance.
     *
     * @var \App\Welkome\Service
     */
    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function view(): View
    {
        return view('app.services.exports.service', [
            'service' => $this->service
        ]);
    }
}
