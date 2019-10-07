<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use App\Helpers\Fields;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTeamMember;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $team = User::find(auth()->user()->id)->employees()
            ->with('hotels')
            ->get(Fields::get('users'));

        return view('app.team.index', compact('team'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hotels = User::find(auth()->user()->id)->hotels()
            ->where('status', true)
            ->get(Fields::get('hotels'));

        $roles = Role::where('name', '!=', 'root')->get(['id', 'name', 'display_name']);

        return view('app.team.create', compact('hotels', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTeamMember $request)
    {
        $member = new User();
        $member->name = $request->name;
        $member->email = $request->email;
        $member->password = Str::random(12);
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
