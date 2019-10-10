<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\Id;
use App\Welkome\Hotel;
use App\Helpers\Fields;
use App\Helpers\Random;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreTeamMember;
use App\Notifications\VerifyTeamMemberEmail;

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
            ->with('headquarters')
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

        $roles = Role::where('name', '!=', 'root')
            ->where('name', '!=', 'manager')
            ->get(['id', 'name']);

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
        // Create temporary password and token
        $password = Str::random(12);
        $token = Random::token(48);

        $member = new User();
        $member->name = $request->name;
        $member->email = $request->email;
        $member->token = $token;
        $member->password = Hash::make($password);
        $member->boss()->associate(auth()->user()->id);

        if ($member->save()) {
            // Assign the team member to work in a headquarter
            $member->headquarters()->attach(Id::get($request->hotel));

            // The hotel
            $hotel = Hotel::find(Id::get($request->hotel), ['id', 'business_name']);

            //Assign role
            $member->assignRole($request->role);

            // Send notification
            $member->notify(new VerifyTeamMemberEmail($member, $hotel, $password));

            flash(trans('common.createdSuccessfully') . '. El usuario debe verificar su correo electrónico.')->success();

            return redirect()->route('team.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('team.index');
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
