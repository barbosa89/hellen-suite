<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use App\Mail\Welcome;
use App\Helpers\Random;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUser;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Mail;

class ReceptionistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::whereHas('roles', function ($query)
        {
            $query->where('name', 'receptionist');
        })->where('parent', auth()->user()->id)->get([
            'id', 'name', 'email', 'status', 'verified'
        ]);

        return view('app.receptionists.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('id', 3)->get(['id', 'name', 'display_name']);

        return view('app.receptionists.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser $request)
    {
        $password = str_random(8);
        $token = Random::token(40);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->token = $token;
        $user->password = bcrypt($password);
        $user->father()->associate(auth()->user()->id);

        if ($user->save()) {
            $user->attachRole(Hashids::decode($request->role)[0]);

            Mail::to($user->email)->send(new Welcome($user, $password));

            flash(trans('users.successful'))->success();

            return redirect()->route('receptionists.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('receptionists.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return abort(404);
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
        return abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = htmlentities($id, ENT_QUOTES);
        $user = User::where('id', Hashids::decode($id)[0])
            ->with([
                'guests' => function ($query)
                {
                    $query->select('id', 'user_id');
                },
                'vehicles' => function ($query)
                {
                    $query->select('id', 'user_id');
                },
                'companies' => function ($query)
                {
                    $query->select('id', 'user_id');
                },
            ])->first(['id', 'name']);
        
        $records = $user->guests->count();
        $records += $user->vehicles->count();
        $records += $user->companies->count();

        if ($records > 0) {
            $user->status = false;

            if ($user->update()) {
                flash(trans('users.wasDisabled'))->success();

                return back();
            }
        } else {
            if ($user->delete()) {
                flash(trans('users.wasDeleted'))->success();

                return back();
            }    
        }

        flash(trans('common.error'))->error();

        return back();
    }
}
