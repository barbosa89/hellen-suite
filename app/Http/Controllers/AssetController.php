<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\Id;
use App\Welkome\Room;
use App\Welkome\Asset;
use Illuminate\Http\Request;
use App\Http\Requests\{StoreAsset, UpdateAsset};

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assets = User::find(auth()->user()->id)->assets()
            ->paginate(config('welkome.paginate'), [
                'id', 'number', 'description', 'brand', 'model', 'reference', 'location', 'user_id', 'created_at'
            ])->sortByDesc('created_at');

        return view('app.assets.index', compact('assets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rooms = Room::doesntHave('assets')
            ->get(['id', 'number', 'description']);
        
        return view('app.assets.create', compact('rooms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAsset $request)
    {
        $asset = new Asset();
        $asset->number = $request->number;
        $asset->description = $request->description;
        $asset->brand = $request->brand;
        $asset->model = $request->model;
        $asset->reference = $request->reference;
        $asset->location = $request->location;
        $asset->user()->associate(auth()->user()->id);

        if ($asset->save()) {
            $asset->rooms()->attach(Id::get($request->room));

            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('assets.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('assets.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $asset = User::find(auth()->user()->id)->assets()
            ->where('id', Id::get($id))
            ->first([
                'id', 'number', 'description', 'brand', 'model', 'reference', 'location', 'user_id'
            ]);

        if (empty($asset)) {
            abort(404);
        }

        $asset->load([
            'rooms' => function ($query) {
                $query->select('id', 'number', 'description');
            }
        ]);

        return view('app.assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $asset = User::find(auth()->user()->id)->assets()
            ->where('id', Id::get($id))
            ->first([
                'id', 'number', 'description', 'brand', 'model', 'reference', 'location', 'user_id'
            ]);

        if (empty($asset)) {
            abort(404);
        }

        $asset->load([
            'rooms' => function ($query) {
                $query->select('id', 'number', 'description');
            }
        ]);

        $rooms = Room::doesntHave('assets')
            ->get(['id', 'number', 'description']);

        return view('app.assets.edit', compact('asset', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAsset $request, $id)
    {
        $asset = User::find(auth()->user()->id)->assets()
            ->where('id', Id::get($id))
            ->first([
                'id', 'number', 'description', 'brand', 'model', 'reference', 'location', 'user_id'
            ]);

        if (empty($asset)) {
            abort(404);
        }

        $asset->number = $request->number;
        $asset->description = $request->description;
        $asset->brand = $request->brand;
        $asset->model = $request->model;
        $asset->reference = $request->reference;
        $asset->location = $request->location;

        if ($asset->update()) {
            $asset->rooms()->sync([]);
            $asset->rooms()->attach(Id::get($request->room));
            
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('assets.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('assets.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $asset = User::find(auth()->user()->id)->assets()
            ->where('id', Id::get($id))
            ->first([
                'id', 'number', 'description', 'brand', 'model', 'reference', 'location', 'user_id'
            ]);

        if (empty($asset)) {
            abort(404);
        }

        if ($asset->delete()) {
            flash(trans('common.deletedSuccessfully'))->success();

            return redirect()->route('assets.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('assets.index');
    }
}
