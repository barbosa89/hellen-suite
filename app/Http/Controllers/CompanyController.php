<?php

namespace App\Http\Controllers;

use App\Exports\CompaniesReport;
use Carbon\Carbon;
use App\Welkome\Company;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Helpers\{Chart, Id, Input, Fields, Response};
use App\Http\Requests\{StoreCompany, UpdateCompany};
use App\Welkome\IdentificationType;
use App\Welkome\Voucher;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

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

        $companies = Company::where('user_id', Id::parent())
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
        $company->is_supplier = (int) $request->is_supplier;
        $company->user()->associate(Id::parent());

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
     * Show the form for creating a new vouche company.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function createForVoucher($id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $types = IdentificationType::all(['id', 'type']);

        return view('app.companies.create-for-voucher', compact('voucher', 'types'));
    }

    /**
     * Store a newly created company in storage and attaching to voucher.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function storeForVoucher(StoreCompany $request, $id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $company = new Company();
        $company->tin = $request->tin;
        $company->business_name = $request->business_name;
        $company->email = $request->get('email', null);
        $company->address = $request->get('address', null);
        $company->phone = $request->get('phone', null);
        $company->mobile = $request->get('mobile', null);
        $company->is_supplier = (int) $request->is_supplier;
        $company->user()->associate(Id::parent());

        if ($company->save()) {
            $voucher->company()->associate($company->id);
            $voucher->save();

            flash(trans('common.successful'))->success();

            return back();
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
        $company = Company::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->first(Fields::get('companies'));

        if (empty($company)) {
            abort(404);
        }

        $vouchers = Voucher::where('user_id', Id::parent())
            ->where('company_id', $company->id)
            ->whereYear('vouchers.created_at', date('Y'))
            ->orderBy('vouchers.created_at', 'DESC')
            ->with([
                'hotel' => function ($query)
                {
                    $query->select('id', 'business_name');
                }
            ])->get(Fields::get('vouchers'));

        $data = Chart::create($vouchers)
            ->addValues()
            ->get();

        return view('app.companies.show', compact('company', 'vouchers', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->first(Fields::get('companies'));

        if (empty($company)) {
            abort(404);
        }

        return view('app.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCompany $request, $id)
    {
        $company = Company::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->first(Fields::get('companies'));

        if (empty($company)) {
            abort(404);
        }

        $company->tin = $request->tin;
        $company->business_name = $request->business_name;
        $company->email = $request->get('email', null);
        $company->address = $request->get('address', null);
        $company->phone = $request->get('phone', null);
        $company->mobile = $request->get('mobile', null);
        $company->is_supplier = (int) $request->is_supplier;

        if ($company->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('companies.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('companies.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = Company::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->whereDoesntHave('vouchers')
            ->first(Fields::get('companies'));

        if (empty($company)) {
            flash(trans('common.notRemovable'))->info();

            return back();
        }

        if ($company->delete()) {
            flash(trans('common.deletedSuccessfully'))->success();

            return redirect()->route('companies.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('companies.index');
    }

    /**
     * Display a listing of searched records.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = Input::clean($request->get('query'));
        $companies = Company::whereLike(['business_name', 'tin'], $query)
            ->where('user_id', Id::parent())
            ->get(Fields::get('companies'));

        if ($request->ajax()) {
            $format = Input::clean($request->get('format'));
            $template = 'app.companies.search.' . Input::clean($request->get('template'));
            $response = new Response($format, $template, $companies);

            return response()->json([
                'companies' => $response->get()
            ]);
        }

        return view('app.companies.search', compact('companies', 'query'));
    }

    /**
     * Export a listing of companies in excel format.
     *
     * @return \Maatwebsite\Excel\Excel
     */
    public function export()
    {
        $companies = Company::where('user_id', Id::parent())
            ->get(Fields::get('companies'));

        if ($companies->isEmpty()) {
            flash(trans('common.noRecords'))->info();

            return redirect()->route('companies.index');
        }

        return Excel::download(new CompaniesReport($companies), trans('companies.title') . '.xlsx');
    }
}
