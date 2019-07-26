<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\Id;
use App\Welkome\Room;
use Illuminate\Http\Request;
use App\Http\Requests\{StoreRoom, UpdateRoom};

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
            ->paginate(config('welkome.paginate'), [
                'id', 'number', 'description', 'price', 'status', 'user_id'
            ])->sort();

        return view('app.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app.rooms.create');
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
        $room->number = $request->number;
        $room->price = $request->price;
        $room->description = $request->description;
        $room->status = '1';
        $room->user()->associate(auth()->user()->id);

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
            ->first([
                'id', 'number', 'description', 'price', 'status', 'user_id'
            ]);

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

        return view('app.rooms.show', compact('room'));
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
            ->first([
                'id', 'number', 'description', 'price', 'status', 'user_id'
            ]);

        if (empty($room)) {
            abort(404);
        }

        return view('app.rooms.edit', compact('room'));
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
            ->first([
                'id', 'number', 'description', 'price', 'status', 'user_id'
            ]);

        if (empty($room)) {
            abort(404);
        }

        $room->number = $request->number;
        $room->price = $request->price;
        $room->description = $request->description;

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
            ->first([
                'id', 'number', 'description', 'price', 'status', 'user_id'
            ]);

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

        return view('app.rooms.index', compact('rooms'));
    }
}
