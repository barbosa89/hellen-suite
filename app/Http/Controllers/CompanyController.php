<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Welkome\Company;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Helpers\{Id, Input, Fields, Response};
use App\Http\Requests\{StoreCompany, UpdateCompany};

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $month = Carbon::now()->subDays(31);

        $companies = Company::where('user_id', auth()->user()->parent)
            ->where('created_at', '>=', $month->toDateTimeString())
            ->paginate(config('welkome.paginate'), Fields::get('companies'))
            ->sortByDesc('created_at');
        
        return view('app.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app.companies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCompany $request)
    {
        $company = new Company();
        $company->tin = $request->tin;
        $company->business_name = $request->business_name;
        $company->email = $request->get('email', null);
        $company->address = $request->get('address', null);
        $company->phone = $request->get('phone', null);
        $company->mobile = $request->get('mobile', null);
        $company->user()->associate(auth()->user()->parent);

        if ($company->save()) {
            flash(trans('common.createdSuccessfully'))->success();
            
            return redirect()->route('companies.show', [
                'id' => Hashids::encode($company->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('companies.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Display a listing of searched records.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {   
        $companies = Company::search(Input::clean($request->get('query')))
            ->where('user_id', auth()->user()->parent)
            ->get(Fields::get('companies'));
            // dd($companies);

        $format = Input::clean($request->get('format'));
        $template = 'app.companies.search.' . Input::clean($request->get('template'));
        $response = new Response($format, $template, $companies);

        if ($request->ajax()) {
            return response()->json([
                'companies' => $response->get()
            ]);
        } else {
            return response()->json([
                'companies' => $response->get()
            ]);
        }
    }
}
