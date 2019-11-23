<?php

namespace App\Http\Controllers;

use App\Exports\VehiclesReport;
use App\Helpers\Fields;
use App\Helpers\Id;
use App\Helpers\Input;
use App\Http\Requests\StoreVehicle;
use App\Http\Requests\StoreVehicleForInvoice;
use App\Http\Requests\UpdateVehicle;
use App\Welkome\Invoice;
use App\Welkome\Vehicle;
use App\Welkome\VehicleType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Vinkla\Hashids\Facades\Hashids;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicles = Vehicle::where('user_id', Id::parent())
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
        $vehicle->type()->associate(Id::get($request->type));
        $vehicle->user()->associate(Id::parent());

        if ($vehicle->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('vehicles.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('vehicles.index');
    }

    /**
     * Show the form for creating a new resource for existing invoice.
     *
     * @return \Illuminate\Http\Response
     */
    public function createForInvoice($id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                }
            ])->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $types = VehicleType::all(['id', 'type']);

        return view('app.vehicles.create-for-invoice', compact('invoice', 'types'));
    }

    /**
     * Store a newly created resource in storage for existing invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeForInvoice(StoreVehicleForInvoice $request, $id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'guests.vehicles' => function ($query) use ($id) {
                    $query->select(Fields::parsed('vehicles'))
                        ->wherePivot('invoice_id', Id::get($id));
                }
            ])->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        if ($invoice->guests->where('id', Id::get($request->guest))->first()->vehicles->isNotEmpty()) {
            flash(trans('invoices.hasVehicles'))->error();

            return redirect()->route('invoices.vehicles.create', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        $existingVehicle = null;
        foreach ($invoice->guests as $guest) {
            if ($guest->vehicles->where('registration', $request->registration)->count() == 1) {
                $existingVehicle = $guest->vehicles->where('registration', $request->registration)->first();
            }
        }

        if (empty($existingVehicle)) {
            $vehicle = new Vehicle();
            $vehicle->registration = $request->registration;
            $vehicle->brand = $request->get('brand', null);
            $vehicle->color = $request->get('color', null);
            $vehicle->type()->associate(Id::get($request->type));
            $vehicle->user()->associate(Id::parent());

            if ($vehicle->save()) {
                $vehicle->guests()->attach($invoice->guests->where('id', Id::get($request->guest))->first()->id, [
                    'invoice_id' => $invoice->id,
                    'created_at' => Carbon::now()->toDateTimeString()
                ]);

                flash(trans('common.createdSuccessfully'))->success();

                return redirect()->route('invoices.vehicles.search', [
                    'id' => Hashids::encode($invoice->id)
                ]);
            }

            flash(trans('common.error'))->error();

            return redirect()->route('invoices.vehicles.create', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        flash(trans('invoices.vehicleAttached'))->error();

        return redirect()->route('invoices.vehicles.create', [
            'id' => Hashids::encode($invoice->id)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vehicle = Vehicle::where('user_id', Id::parent())
            ->where('id', Id::get($id))
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
        $vehicle = Vehicle::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->first(Fields::get('vehicles'));

        if (empty($vehicle)) {
            abort(404);
        }

        $vehicle->registration = $request->registration;
        $vehicle->brand = $request->get('brand', null);
        $vehicle->color = $request->get('color', null);
        $vehicle->type()->associate(Id::get($request->type));

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
        $vehicle = Vehicle::where('user_id', Id::parent())
            ->where('id', Id::get($id))
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
        $query = Input::clean($request->get('query', null));

        if (empty($query)) {
            abort(404);
        }

        $vehicles = Vehicle::where('user_id', Id::parent())
            ->whereLike(['registration', 'brand', 'color', 'type.type'], $query)
            ->get(Fields::get('vehicles'));

        if ($request->ajax()) {
            $vehicles = $vehicles->map(function ($vehicle)
            {
                $vehicle->user_id = Hashids::encode($vehicle->user_id);
                $vehicle->vehicle_type_id = Hashids::encode($vehicle->vehicle_type_id);

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
        $vehicles = Vehicle::where('user_id', Id::parent())
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
