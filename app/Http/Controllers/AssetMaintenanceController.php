<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\View\View;
use App\Models\Maintenance;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreMaintenance;
use Illuminate\Support\Facades\Storage;

class AssetMaintenanceController extends Controller
{
    public function create(string $asset): View
    {
        $asset = Asset::whereOwner()
            ->where('id', id_decode($asset))
            ->with([
                'room' => function ($query) {
                    $query->select('id', 'number');
                },
                'hotel' => function ($query) {
                    $query->select('id', 'business_name');
                },
            ])
            ->firstOrFail(fields_get('assets'));

        return view('app.assets.maintenances.create', compact('asset'));
    }

    public function store(StoreMaintenance $request, string $asset): RedirectResponse
    {
        $asset = Asset::whereOwner()
            ->where('id', id_decode($asset))
            ->firstOrFail(fields_get('assets'));

        $maintenance = new Maintenance();
        $maintenance->date = $request->input('date');
        $maintenance->commentary = $request->input('commentary');
        $maintenance->value = $request->input('value');
        $maintenance->user()->associate(id_parent());

        if ($request->hasFile('invoice')) {
            $file = $request->file('invoice');

            $path = $file->storeAs('public', $file->hashName());

            $maintenance->invoice = $path;
        }

        $asset->maintenances()->save($maintenance);

        flash(trans('common.createdSuccessfully'))->success();

        return redirect()->route('assets.show', [
            'id' => $asset->hash,
        ]);
    }

    public function edit(string $asset, string $maintenance): View
    {
        $maintenance = Maintenance::whereOwner()
            ->whereMaintainable($asset, Asset::class)
            ->where('id', id_decode($maintenance))
            ->with([
                'maintainable',
                'maintainable.room' => function ($query) {
                    $query->select('id', 'number');
                },
                'maintainable.hotel' => function ($query) {
                    $query->select('id', 'business_name');
                },
            ])
            ->firstOrFail(fields_get('maintenances'));

        return view('app.assets.maintenances.edit', compact('maintenance'));
    }

    public function update(StoreMaintenance $request, string $asset, string $maintenance): RedirectResponse
    {
        $maintenance = Maintenance::whereOwner()
            ->whereMaintainable($asset, Asset::class)
            ->where('id', id_decode($maintenance))
            ->with('maintainable')
            ->firstOrFail(fields_get('maintenances'));

        $maintenance->date = $request->date;
        $maintenance->commentary = $request->commentary;
        $maintenance->value = $request->get('value', null);

        if ($request->hasFile('invoice')) {
            if (!empty($maintenance->invoice)) {
                Storage::delete($maintenance->invoice);
            }

            $file = $request->file('invoice');

            $path = $file->storeAs('public', $file->hashName());

            $maintenance->invoice = $path;
        }

        $maintenance->save();

        flash(trans('common.updatedSuccessfully'))->success();

        return redirect()->route('assets.show', [
            'id' => $maintenance->maintainable->hash
        ]);
    }

    public function destroy(string $asset, string $maintenance): RedirectResponse
    {
        $maintenance = Maintenance::whereOwner()
            ->whereMaintainable($asset, Asset::class)
            ->where('id', id_decode($maintenance))
            ->with('maintainable')
            ->firstOrFail(fields_get('maintenances'));

        $maintenance->delete();

        Storage::delete($maintenance->invoice);

        flash(trans('common.deletedSuccessfully'))->success();

        return redirect()->route('assets.show', [
            'id' => $maintenance->maintainable->hash
        ]);
    }
}
