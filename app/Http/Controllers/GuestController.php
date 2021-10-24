<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Helpers\Chart;
use App\Events\CheckIn;
use App\Models\Voucher;
use App\Events\CheckOut;
use Illuminate\View\View;
use App\Exports\GuestsReport;
use App\Actions\Guests\CreateAction;
use App\Actions\Guests\UpdateAction;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use App\View\Models\Guests\EditViewModel;
use App\Http\Requests\Guests\StoreRequest;
use App\View\Models\Guests\CreateViewModel;

class GuestController extends Controller
{
    public function index(): View
    {
        return view('app.guests.index');
    }

    public function create(): View
    {
        return view('app.guests.create', new CreateViewModel());
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $guest = CreateAction::execute($request->validated());

        flash(trans('common.created.successfully'))->success();

        return redirect()->route('guests.show', [
            'id' => $guest->hash,
        ]);
    }

    public function show($id): View
    {
        $guest = Guest::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->firstOrFail(fields_get('guests'));

        $guest->load([
            'vouchers' => function ($query) {
                $query->select(fields_dotted('vouchers'))
                    ->whereYear('vouchers.created_at', date('Y'))
                    ->orderBy('vouchers.created_at', 'DESC');
            },
            'vouchers.hotel' => function ($query) {
                $query->select('id', 'business_name');
            },
            'country' => function ($query) {
                $query->select('id', 'name');
            },
        ]);

        $data = Chart::create($guest->vouchers)
            ->countVouchers()
            ->get();

        return view('app.guests.show', compact('guest', 'data'));
    }

    public function edit(string $id): View
    {
        return view('app.guests.edit', new EditViewModel(id_decode($id)));
    }

    public function update(StoreRequest $request, string $id): RedirectResponse
    {
        $guest = Guest::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->firstOrFail(fields_get('guests'));

        UpdateAction::execute($request->validated(), $guest);

        flash(trans('common.updatedSuccessfully'))->success();

        return redirect()->route('guests.show', [
            'id' => $guest->hash,
        ]);
    }

    public function destroy(string $id): RedirectResponse
    {
        $guest = Guest::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->withCount('vouchers')
            ->firstOrFail(fields_get('guests'));

        if ($guest->vouchers_count) {
            flash(trans('common.notRemovable'))->info();

            return redirect()->route('guests.show', [
                'id' => $guest->hash,
            ]);
        }

        $guest->delete();

        flash(trans('common.deletedSuccessfully'))->success();

        return redirect()->route('guests.index');
    }

    /**
     * @return Illuminate\Http\RedirectResponse|\Maatwebsite\Excel\Excel
     */
    public function export()
    {
        $guests = Guest::where('user_id', id_parent())
            ->with([
                'identificationType' => function ($query)
                {
                    $query->select(['id', 'type']);
                },
                'country' => function ($query)
                {
                    $query->select(['id', 'name']);
                }
            ])
            ->get(fields_get('guests'));

        if ($guests->isEmpty()) {
            flash(trans('common.noRecords'))->info();

            return redirect()->route('guests.index');
        }

        return Excel::download(new GuestsReport($guests), trans('guests.title') . '.xlsx');
    }

    public function toggle(string $id, string $voucher): RedirectResponse
    {
        $voucher = Voucher::where('user_id', id_parent())
            ->where('id', id_decode($voucher))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select(fields_dotted('guests'))
                        ->where('responsible_adult', false)
                        ->withPivot('main', 'active');
                },
                'guests.rooms' => function ($query) use ($voucher) {
                    $query->select(fields_dotted('rooms'))
                        ->wherePivot('voucher_id', id_decode($voucher));
                },
                'guests.identificationType' => function ($query) {
                    $query->select('id', 'type');
                },
                'rooms' => function ($query) {
                    $query->select(fields_dotted('rooms'))
                        ->withPivot('enabled');
                },
                'hotel' => function ($query) {
                    $query->select(fields_get('hotels'));
                }
            ])->firstOrFail(fields_dotted('vouchers'));

        // Check if the voucher only has a guest
        if ($voucher->guests->count() == 1) {
            flash(trans('vouchers.onlyOne'))->error();

            return redirect()->route('vouchers.show', [
                'id' => $voucher->hash,
            ]);
        }

        // The guest
        $guest = $voucher->guests->where('id', id_decode($id))->first();

        // The guest room
        $room = $voucher->rooms->where('id', $guest->rooms->first()->id)->first();

        // Check if the room is available in the voucher
        if ($room->pivot->enabled) {
            // Toggle status
            // The guest leaves the hotel but remains on the voucher
            if ($guest->status == true and $guest->pivot->active == true) {
                $guest->status = false;

                $voucher->guests()->updateExistingPivot(
                    $guest,
                    [
                        'active' => false
                    ]
                );


            }

            // The guest enters the hotel at the same voucher
            if ($guest->status == false and $guest->pivot->active == false) {
                $guest->status = true;

                $voucher->guests()->updateExistingPivot(
                    $guest,
                    [
                        'active' => true
                    ]
                );
            }

            if ($guest->save()) {
                if ($guest->status) {
                    CheckIn::dispatch($voucher, $guest, $room);
                } else {
                    CheckOut::dispatch($voucher, $guest, $room);
                }

                // Check if is the main guest
                if ($guest->pivot->main) {
                    // Update main guest in the voucher
                    $voucher->guests()->updateExistingPivot(
                        $guest,
                        ['main' => false]
                    );

                    // The new main guest
                    $main = $voucher->guests->where('id', '!=', id_decode($id))
                        ->where('pivot.active', true)
                        ->where('status', true)
                        ->first();

                    // Select the main guest
                    $voucher->guests()->updateExistingPivot(
                        $main,
                        ['main' => true]
                    );
                }

                flash(trans('common.updatedSuccessfully'))->success();

                return redirect()->route('vouchers.show', [
                    'id' => $voucher->hash,
                ]);
            }
        }

        flash(trans('common.error'))->error();

        return redirect()->route('vouchers.show', [
            'id' => $voucher->hash,
        ]);
    }
}
