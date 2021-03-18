<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Contracts\CompanyRepository;
use App\Http\Controllers\Controller;
use App\Models\Company;

class CompanyController extends Controller
{
    public CompanyRepository $company;

    public function __construct(CompanyRepository $company)
    {
        $this->company = $company;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $validated = request()->validate([
            'from_date' => 'bail|nullable|date|before_or_equal:today'
        ]);

        $companies = $this->company->paginate(
            request()->get('per_page', 15),
            Arr::only($validated, Company::SCOPE_FILTERS),
        );

        return response()->json([
            'companies' => $companies,
        ]);
    }
}
