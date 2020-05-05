<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use App\Mail\Welcome;
use App\Helpers\Random;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUser;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::whereHas('roles', function($query) {
                $query->where('name', 'manager');
            })->with([
                'employees' => function ($query) {
                    $query->select('id', 'name', 'parent');
                }
            ])->get(['id', 'name', 'email', 'status', 'created_at', 'email_verified_at']);

        return view('app.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::whereIn('id', [1, 2])->get(['id', 'name', 'display_name']);

        return view('app.users.create', compact('roles'));
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

        if ($user->save()) {
            $user->attachRole(Hashids::decode($request->role)[0]);

            Mail::to($user->email)->send(new Welcome($user, $password));

            flash(trans('users.successful'))->success();

            return redirect()->route('users.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('users.index');
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
