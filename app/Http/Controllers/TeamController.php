<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Hotel;
use App\Helpers\Permissions;
use App\Helpers\Random;
use App\Http\Requests\AssignTeamMember;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreTeamMember;
use App\Notifications\VerifyTeamMemberEmail;
use Spatie\Permission\Models\Permission;

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
            ])->get(fields_get('users'));

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
            ->get(fields_get('hotels'));

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
            // Assign the team member to work in a headquarters
            $member->headquarters()->attach(id_decode($request->hotel));

            // The hotel
            $hotel = Hotel::find(id_decode($request->hotel), ['id', 'business_name']);

            //Assign role
            $member->assignRole($request->role);

            // Assign permissions
            $member->syncPermissions(Permissions::list($request->role));

            // Send notification
            $member->notify(new VerifyTeamMemberEmail($member, $hotel, $password));

            flash(trans('common.createdSuccessfully') . '. El usuario debe verificar su correo electrónico.')->success();

            return redirect()->route('team.permissions', ['id' => id_encode($member->id)]);
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
        $member = User::where('parent', auth()->user()->id)
            ->where('id', id_decode($id))
            ->first(fields_get('users'));

        if (empty($member)) {
            abort(404);
        }

        $member->load([
            'headquarters' => function($query) {
                $query->select(['id', 'business_name']);
            },
            'roles' => function ($query)
            {
                $query->select(['id', 'name']);
            },
            'shifts' => function ($query)
            {
                $query->select(fields_get('shifts'));
            },
            'shifts.hotel' => function ($query)
            {
                $query->select(fields_get('hotels'));
            }
        ]);

        return view('app.team.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $member = User::where('parent', auth()->user()->id)
            ->where('id', id_decode($id))
            ->with('roles')
            ->first(fields_get('users'));

        $roles = Role::whereNotIn('name', ['root', 'manager', $member->roles->first()->name])
            ->get(['id', 'name']);

        return view('app.team.edit', compact('member', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $member = User::where('parent', auth()->user()->id)
            ->where('id', id_decode($id))
            ->first(fields_get('users'));

        $member->name = $request->name;

        if ($member->save()) {
            $member->roles()->sync([]);

            $member->assignRole($request->role);

            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('team.show', ['id' => id_encode($member->id)]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('team.index');
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
            ->where('id', id_decode($id))
            ->with([
                'roles' => function($query) {
                    $query->select(['id', 'name']);
                }
            ])->first(fields_get('users'));

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
            ->where('id', id_decode($id))
            ->with([
                'headquarters' => function($query) {
                    $query->select(['id', 'business_name']);
                }
            ])->first(fields_get('users'));

        // Check if the team member has an assigned headquarters,
        // if the team member has a headquarters, then all headquarters are queried
        // except the current headquarters
        $hasHeadquarters = $member->headquarters->isNotEmpty();

        $hotels = User::find(auth()->user()->id, ['id'])->hotels()
            ->when($hasHeadquarters, function ($query) use ($member)
            {
                $query->where('id', '!=', $member->headquarters()->first()->id);
            })->get(fields_get('hotels'));

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
     * Attach hotel headquarters to the team member.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attach(AssignTeamMember $request, $id)
    {
        $member = User::find(auth()->user()->id, ['id'])->employees()
            ->where('id', id_decode($id))
            ->first(fields_get('users'));

        // Delete the old relationship
        $member->headquarters()->sync([]);

        $member->headquarters()->attach(id_decode($request->hotel));

        flash(trans('common.updatedSuccessfully'))->success();

        return redirect()->route('team.show', [
            'id' => id_encode($member->id)
        ]);
    }

    /**
     * Show the form for assign permissions to team member.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissions($id)
    {
        // Team member receiving permissions
        $member = User::find(auth()->user()->id, ['id'])->employees()
            ->where('id', id_decode($id))
            ->with([
                'roles' => function($query) {
                    $query->select(['id', 'name', 'guard_name']);
                },
                'permissions' => function($query) {
                    $query->select(['id', 'name', 'guard_name']);
                }
            ])->first(fields_get('users'));

        // All permissions from database
        $allPermissions = Permission::get(['id', 'name', 'guard_name']);

        // Grouping by modules
        $permissions = $allPermissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('app.team.permissions', compact('member', 'permissions'));
    }

    /**
     * Attach hotel headquarters to the team member.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storePermissions(Request $request, $id)
    {
        // Team member receiving permissions
        $member = User::find(auth()->user()->id, ['id'])->employees()
            ->where('id', id_decode($id))
            ->first(fields_get('users'));

        // Checked permissions from database
        $permissions = Permission::whereIn('id', id_decode_recursive($request->permissions))
            ->get(['id', 'name', 'guard_name']);

        // Delete all old permissions
        $member->syncPermissions([]);

        // Assign checked permissions
        $member->syncPermissions($permissions);

        flash(trans('common.updatedSuccessfully'))->success();

        return back();
    }
}
