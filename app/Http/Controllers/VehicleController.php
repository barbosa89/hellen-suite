<?php

namespace App\Http\Controllers;

use App\Exports\VehiclesReport;
use App\Helpers\Fields;
use App\Helpers\Parameter;
use App\Http\Requests\StoreVehicle;
use App\Http\Requests\StoreVehicleForVoucher;
use App\Http\Requests\UpdateVehicle;
use App\Welkome\Voucher;
use App\Welkome\Vehicle;
use App\Welkome\VehicleType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicles = Vehicle::where('user_id', id_parent())
            ->with([
                'type' => function ($query)
                {
                    $query->select(['id', 'type']);
                }
            ])->orderBy('created_at', 'DESC')
            ->limit(100)
            ->paginate(config('welkome.paginate'), Fields::get('vehicles'));

        return view('app.vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = VehicleType::all(['id', 'type']);

        return view('app.vehicles.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVehicle $request)
    {
        $vehicle = new Vehicle();
        $vehicle->registration = $request->registration;
        $vehicle->brand = $request->get('brand', null);
        $vehicle->color = $request->get('color', null);
        $vehicle->type()->associate(id_decode($request->type));
        $vehicle->user()->associate(id_parent());

        if ($vehicle->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('vehicles.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('vehicles.index');
    }

    /**
     * Show the form for creating a new resource for existing voucher.
     *
     * @return \Illuminate\Http\Response
     */
    public function createForVoucher($id)
    {
        $voucher = Voucher::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                }
            ])->first(Fields::get('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $types = VehicleType::all(['id', 'type']);

        return view('app.vehicles.create-for-voucher', compact('voucher', 'types'));
    }

    /**
     * Store a newly created resource in storage for existing voucher.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeForVoucher(StoreVehicleForVoucher $request, $id)
    {
        $voucher = Voucher::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'guests.vehicles' => function ($query) use ($id) {
                    $query->select(Fields::parsed('vehicles'))
                        ->wherePivot('voucher_id', id_decode($id));
                }
            ])->first(Fields::get('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        if ($voucher->guests->where('id', id_decode($request->guest))->first()->vehicles->isNotEmpty()) {
            flash(trans('vouchers.hasVehicles'))->error();

            return redirect()->route('vouchers.vehicles.create', [
                'id' => id_encode($voucher->id)
            ]);
        }

        $existingVehicle = null;
        foreach ($voucher->guests as $guest) {
            if ($guest->vehicles->where('registration', $request->registration)->count() == 1) {
                $existingVehicle = $guest->vehicles->where('registration', $request->registration)->first();
            }
        }

        if (empty($existingVehicle)) {
            $vehicle = new Vehicle();
            $vehicle->registration = $request->registration;
            $vehicle->brand = $request->get('brand', null);
            $vehicle->color = $request->get('color', null);
            $vehicle->type()->associate(id_decode($request->type));
            $vehicle->user()->associate(id_parent());

            if ($vehicle->save()) {
                $vehicle->guests()->attach($voucher->guests->where('id', id_decode($request->guest))->first()->id, [
                    'voucher_id' => $voucher->id,
                    'created_at' => Carbon::now()->toDateTimeString()
                ]);

                flash(trans('common.createdSuccessfully'))->success();

                return redirect()->route('vouchers.vehicles.search', [
                    'id' => id_encode($voucher->id)
                ]);
            }

            flash(trans('common.error'))->error();

            return redirect()->route('vouchers.vehicles.create', [
                'id' => id_encode($voucher->id)
            ]);
        }

        flash(trans('vouchers.vehicleAttached'))->error();

        return redirect()->route('vouchers.vehicles.create', [
            'id' => id_encode($voucher->id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vehicle = Vehicle::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->first(Fields::get('vehicles'));

        if (empty($vehicle)) {
            abort(404);
        }

        $vehicle->load([
            'type' => function ($query)
            {
                $query->select(['id', 'type']);
            }
        ]);

        $types = VehicleType::where('id', '!=', $vehicle->type->id)
            ->get(['id', 'type']);

        return view('app.vehicles.edit', compact('vehicle', 'types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVehicle $request, $id)
    {
        $vehicle = Vehicle::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->first(Fields::get('vehicles'));

        if (empty($vehicle)) {
            abort(404);
        }

        $vehicle->registration = $request->registration;
        $vehicle->brand = $request->get('brand', null);
        $vehicle->color = $request->get('color', null);
        $vehicle->type()->associate(id_decode($request->type));

        if ($vehicle->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('vehicles.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('vehicles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vehicle = Vehicle::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->whereDoesntHave('guests')
            ->first(Fields::get('vehicles'));

        if (empty($vehicle)) {
            flash(trans('common.notRemovable'))->info();

            return back();
        }

        if ($vehicle->delete()) {
            flash(trans('common.deletedSuccessfully'))->success();

            return redirect()->route('vehicles.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('vehicles.index');
    }

    /**
     * Display a listing of searched records.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = Parameter::clean($request->get('query', null));

        if (empty($query)) {
            abort(404);
        }

        $vehicles = Vehicle::where('user_id', id_parent())
            ->whereLike(['registration', 'brand', 'color', 'type.type'], $query)
            ->get(Fields::get('vehicles'));

        if ($request->ajax()) {
            $vehicles = $vehicles->map(function ($vehicle)
            {
                $vehicle->user_id = id_encode($vehicle->user_id);
                $vehicle->vehicle_type_id = id_encode($vehicle->vehicle_type_id);

                return $vehicle;
            });

            return response()->json([
                'data' => $vehicles->toJson()
            ]);
        }

        return view('app.vehicles.search', compact('vehicles', 'query'));
    }

    /**
     * Export a listing of vehicles in excel format.
     *
     * @return \Maatwebsite\Excel\Excel
     */
    public function export()
    {
        $vehicles = Vehicle::where('user_id', id_parent())
            ->with([
                'type' => function ($query)
                {
                    $query->select(['id', 'type']);
                },
                'guests' => function ($query)
                {
                    $query->select(['id', 'name', 'last_name']);
                }
            ])->get(Fields::get('vehicles'));

        if ($vehicles->isEmpty()) {
            flash(trans('common.noRecords'))->info();

            return redirect()->route('vehicles.index');
        }

        return Excel::download(new VehiclesReport($vehicles), trans('vehicles.title') . '.xlsx');
    }
}
