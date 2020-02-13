<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\Id;
use App\Welkome\Hotel;
use App\Helpers\Fields;
use App\Helpers\Random;
use App\Http\Requests\AssignTeamMember;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreTeamMember;
use App\Notifications\VerifyTeamMemberEmail;
use Spatie\Permission\Models\Permission;
use Vinkla\Hashids\Facades\Hashids;

// TODO: Implementar la asignación de permisos directos a usuarios, con los roles se cargan módulos exclusivos
class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $team = User::where('parent', auth()->user()->id)
            ->with([
                'headquarters' => function($query) {
                    $query->select(['id', 'business_name']);
                },
                'roles' => function ($query)
                {
                    $query->select(['id', 'name']);
                }
            ])
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

        // Check if is empty
        if ($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return back();
        }

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

            return redirect()->route('team.permissions', ['id' => Hashids::encode($member->id)]);
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
        //TODO: Implementar el show del team member
        flash('Característica en construcción')->info();

        return redirect()->route('team.index');
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
        $member = User::find(auth()->user()->id, ['id'])->employees()
            ->where('id', Id::get($id))
            ->with([
                'roles' => function($query) {
                    $query->select(['id', 'name']);
                }
            ])->first(Fields::get('users'));

        $member->removeRole($member->roles()->first()->name);

        if ($member->delete()) {
            flash(trans('common.deletedSuccessfully'))->success();

            return redirect()->route('team.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('team.index');
    }

    /**
     * Show the form for assign a the team member to headquarte.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assign($id)
    {
        $member = User::find(auth()->user()->id, ['id'])->employees()
            ->where('id', Id::get($id))
            ->with([
                'headquarters' => function($query) {
                    $query->select(['id', 'business_name']);
                }
            ])->first(Fields::get('users'));

        // Check if the team member has an assigned headquarter,
        // if the team member has a headquarter, then all headquarters are queried
        // except the current headquarter
        $hasHeadquarter = $member->headquarters->isNotEmpty();

        $hotels = User::find(auth()->user()->id, ['id'])->hotels()
            ->when($hasHeadquarter, function ($query) use ($member)
            {
                $query->where('id', '!=', $member->headquarters()->first()->id);
            })->get(Fields::get('hotels'));

        // Check if is empty
        if ($hotels->isEmpty() and $member->headquarters->isNotEmpty()) {
            flash('Sólo hay un hotel creado y ya fue asignado.')->info();

            return back();
        }

        // Check if is empty
        if ($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('hotels.index');
        }

        return view('app.team.assign', compact('member', 'hotels'));
    }

    /**
     * Attach hotel headquarter to the team member.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attach(AssignTeamMember $request, $id)
    {
        $member = User::find(auth()->user()->id, ['id'])->employees()
            ->where('id', Id::get($id))
            ->first(Fields::get('users'));

        // Delete the old relationship
        $member->headquarters()->sync([]);

        $member->headquarters()->attach(Id::get($request->hotel));

        flash(trans('common.updatedSuccessfully'))->success();

        return redirect()->route('team.show', [
            'id' => Hashids::encode($member->id)
        ]);
    }

    /**
     * Show the form for assign a the team member to headquarte.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissions($id)
    {
        // Team member receiving permissions
        $member = User::find(auth()->user()->id, ['id'])->employees()
            ->where('id', Id::get($id))
            ->with([
                'roles' => function($query) {
                    $query->select(['id', 'name', 'guard_name']);
                },
                'permissions' => function($query) {
                    $query->select(['id', 'name', 'guard_name']);
                }
            ])->first(Fields::get('users'));

        // All permissions from database
        $allPermissions = Permission::get(['id', 'name', 'guard_name']);

        // Grouping by modules
        $permissions = $allPermissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('app.team.permissions', compact('member', 'permissions'));
    }

    /**
     * Attach hotel headquarter to the team member.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storePermissions(Request $request, $id)
    {
        // Team member receiving permissions
        $member = User::find(auth()->user()->id, ['id'])->employees()
            ->where('id', Id::get($id))
            ->first(Fields::get('users'));

        // Checked permissions from database
        $permissions = Permission::whereIn('id', Id::get($request->permissions))
            ->get(['id', 'name', 'guard_name']);

        // Delete all old permissions
        $member->syncPermissions([]);

        // Assign checked permissions
        $member->syncPermissions($permissions);

        flash(trans('common.updatedSuccessfully'))->success();

        return back();
    }
}
