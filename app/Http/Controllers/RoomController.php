<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\Id;
use App\Welkome\Room;
use App\Helpers\Input;
use App\Helpers\Fields;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRoom;
use App\Http\Requests\UpdateRoom;
use Vinkla\Hashids\Facades\Hashids;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource for admin users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::where('user_id', auth()->user()->id)
            ->paginate(config('welkome.paginate', Fields::get('rooms')))->sort();

        return view('app.rooms.admin.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app.rooms.admin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoom $request)
    {
        $room = new Room();
        $room->floor = (int) $request->floor;
        $room->number = $request->number;
        $room->price = (float) $request->price;
        $room->min_price = (float) $request->min_price;
        $room->description = $request->description;
        $room->status = '1';
        $room->capacity = (int) $request->capacity;

        if (in_array((int) $request->tax_status, [1,2])) {
            $room->tax_status = $request->tax_status;
            $room->tax = (float) $request->tax;
        }

        $room->user()->associate(auth()->user()->id);
        $room->is_suite = (int) $request->type;

        if ($room->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('rooms.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('rooms.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $room = User::find(auth()->user()->id)->rooms()
            ->where('id', Id::get($id))
            ->first(Fields::get('rooms'));

        if (empty($room)) {
            abort(404);
        }

        $room->load([
            'assets' => function ($query)
            {
                $query->select('id', 'number', 'description', 'brand', 'model', 'reference');
            },
            'products' => function ($query)
            {
                $query->select('id', 'description', 'brand', 'reference', 'price');
            },
        ]);

        return view('app.rooms.admin.show', compact('room'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $room = User::find(auth()->user()->id)->rooms()
            ->where('id', Id::get($id))
            ->first(Fields::get('rooms'));

        if (empty($room)) {
            abort(404);
        }

        return view('app.rooms.admin.edit', compact('room'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoom $request, $id)
    {
        $room = User::find(auth()->user()->id)->rooms()
            ->where('id', Id::get($id))
            ->first(Fields::get('rooms'));

        if (empty($room)) {
            abort(404);
        }

        $room->number = $request->number;
        $room->price = $request->price;
        $room->description = $request->description;
        $room->min_price = (float) $request->min_price;
        $room->description = $request->description;
        $room->status = '1';
        $room->capacity = (int) $request->capacity;

        if (in_array((int) $request->tax_status, [1,2])) {
            $room->tax_status = $request->tax_status;
            $room->tax = (float) $request->tax;
        } else {
            $room->tax_status = "0";
            $room->tax = 0.0;
        }

        $room->is_suite = (int) $request->type;

        if ($room->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('rooms.show', [
                'room' => Hashids::encode($room->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('rooms.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $room = User::find(auth()->user()->id)->rooms()
            ->where('id', Id::get($id))
            ->first(Fields::get('rooms'));

        if (empty($room)) {
            abort(404);
        }

        $room->load([
            'invoices' => function ($query)
            {
                $query->select('id');
            },
        ]);

        if ($room->invoices->count() > 0) {
            $room->status = '3';

            if ($room->update()) {
                flash(trans('rooms.wasDisabled'))->success();

                return redirect()->route('rooms.show', [
                    'room' => Hashids::encode($room->id)
                ]);
            }
        } else {
            if ($room->delete()) {
                flash(trans('common.deletedSuccessfully'))->success();

                return redirect()->route('rooms.index');
            }
        }

        flash(trans('common.error'))->error();

        return redirect()->route('rooms.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = Input::clean($request->get('query', null));

        if (empty($query)) {
            return redirect()->route('rooms.index');
        }

        $rooms = User::find(auth()->user()->id)->rooms()
            ->whereLike(['number', 'description'], $query)
            ->paginate(20, Fields::get('rooms'));

        return view('app.rooms.admin.search', compact('rooms', 'query'));
    }

    /**
     * Display a listing of the resource for receptionists users.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $rooms = Room::where('user_id', auth()->user()->parent)
            ->paginate(config('welkome.paginate'), [
                'id', 'number', 'description', 'price', 'status', 'user_id', 'parent'
            ])->sort();

        return view('app.rooms.receptionist.index', compact('rooms'));
    }
}
