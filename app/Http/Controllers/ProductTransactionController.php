<?php

namespace App\Http\Controllers;

use App\Helpers\Fields;
use App\Helpers\Id;
use App\User;
use App\Welkome\Hotel;
use Illuminate\Http\Request;

class ProductTransactionController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hotels = $this->getHotels();

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('products.index');
        }

        return view('app.products.transactions.create', compact('hotels'));
    }

    /**
     * Return hotel list
     *
     * @return  \Illuminate\Support\Collection
     */
    private function getHotels()
    {
        if (auth()->user()->hasRole('receptionist')) {
            $user = auth()->user()->load([
                'headquarters' => function ($query)
                {
                    $query->select(Fields::parsed('hotels'))
                        ->where('status', true);
                }
            ]);

            return $user->headquarters;
        }

        $hotels = User::find(Id::parent(), ['id'])
            ->hotels()
            ->where('status', true)
            ->get(Fields::get('hotels'));

        return $hotels;
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
