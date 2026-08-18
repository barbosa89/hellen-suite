<?php

namespace App\Http\Controllers;

use App\Contracts\RoomRepository;
use App\Helpers\Chart;
use App\Http\Requests\ChangeRoomStatus;
use App\Http\Requests\StoreRoom;
use App\Http\Requests\UpdateRoom;
use App\Models\Room;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function __construct(public RoomRepository $room) {}

    public function index(): View
    {
        return view('app.rooms.index');
    }

    public function create(): RedirectResponse|View
    {
        $hotels = User::find(id_parent(), ['id'])
            ->hotels()
            ->get(fields_get('hotels'));

        if ($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('hotels.index');
        }

        return view('app.rooms.create', compact('hotels'));
    }

    public function store(StoreRoom $request): RedirectResponse
    {
        $room = $this->room->create($request->hotel_id, $request->validated());

        flash(trans('common.createdSuccessfully'))->success();

        return redirect()->route('rooms.show', ['id' => id_encode($room->id)]);
    }

    public function show(string $id): View
    {
        $room = $this->room->find(id_decode($id));

        $room->load([
            'assets' => function ($query): void {
                $query->select(fields_dotted('assets'));
            },
            'products' => function ($query): void {
                $query->select(fields_dotted('products'));
            },
            'vouchers' => function ($query): void {
                $query->select(fields_dotted('vouchers'))
                    ->orderBy('vouchers.created_at', 'DESC')
                    ->limit(20)
                    ->withPivot('value');
            },
        ]);

        $data = Chart::create($room->vouchers)
            ->addItemValues()
            ->get();

        return view('app.rooms.show', compact('room', 'data'));
    }

    public function edit(string $id): View
    {
        $room = $this->room->find(id_decode($id));

        return view('app.rooms.edit', compact('room'));
    }

    public function update(UpdateRoom $request, string $id): RedirectResponse
    {
        $room = $this->room->update(id_decode($id), $request->validated());

        flash(trans('common.updatedSuccessfully'))->success();

        return redirect()->route('rooms.show', [
            'id' => id_encode($room->id),
        ]);
    }

    public function destroy(string $id): RedirectResponse
    {
        if ($this->room->destroy(id_decode($id))) {
            flash(trans('common.deletedSuccessfully'))->success();

            return redirect()->route('rooms.index');
        }

        flash(trans('rooms.cannot.destroy'))->error();

        return redirect()->route('rooms.show', [
            'id' => $id,
        ]);
    }

    public function search(Request $request): RedirectResponse|View
    {
        $query = clean_param($request->get('query', null));

        if (empty($query)) {
            return redirect()->route('rooms.index');
        }

        $rooms = $this->room->search($query);

        return view('app.rooms.search', compact('rooms', 'query'));
    }

    public function getPrice(Request $request): JsonResponse
    {
        $room = Room::where('user_id', id_parent())
            ->where('hotel_id', id_decode($request->hotel))
            ->where('number', $request->number)
            ->where('status', Room::AVAILABLE) // It is free
            ->firstOrFail(fields_get('rooms'));

        return response()->json([
            'price' => $room->price,
            'min_price' => $room->min_price,
            'tax' => $room->tax,
        ]);
    }

    public function toggle(ChangeRoomStatus $request): RedirectResponse
    {
        $room = $this->room->toggle(id_decode($request->room), $request->status);

        return redirect()->route('rooms.show', [
            'id' => id_encode($room->id),
        ]);
    }
}
