<?php

namespace App\Http\Controllers\Api;

use App\Models\Voucher;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Contracts\VoucherRepository;
use App\Http\Controllers\Controller;

class VoucherController extends Controller
{
    public VoucherRepository $voucher;

    public function __construct(VoucherRepository $voucher)
    {
        $this->voucher = $voucher;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  string $hotel
     * @return \Illuminate\Http\Response
     */
    public function index(string $hotel)
    {
        $validated = request()->validate([
            'from_date' => 'bail|nullable|date|before_or_equal:today'
        ]);

        $vouchers = $this->voucher->paginate(
            id_decode($hotel),
            request()->get('per_page', 15),
            Arr::only($validated, Voucher::SCOPE_FILTERS),
        );

        return response()->json([
            'vouchers' => $vouchers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
