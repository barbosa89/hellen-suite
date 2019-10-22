<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Vinkla\Hashids\Facades\Hashids;
use App\Helpers\{Id, Input, Fields};
use App\Welkome\{Guest, IdentificationType};

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $guests = Guest::paginate();
        // $month = Carbon::now()->subDays(31);

        // $registered = Guest::where('user_id', Id::parent())
        //     ->where('status', false) # Not in hotel
        //     ->where('created_at', '>=', $month->toDateTimeString())
        //     ->paginate(config('welkome.paginate'), Fields::get('guests'))
        //     ->sortBy('created_at');

        // $in = Guest::where('user_id', Id::parent())
        //     ->where('status', true) # In hotel
        //     ->with([
        //         'rooms' => function ($query) {
        //             $query->select(Fields::parsed('rooms'));
        //         },
        //         'invoices' => function ($query) {
        //             $query->select('id', 'number')
        //                 ->where('open', true);
        //         },
        //     ])->paginate(config('welkome.paginate'), Fields::get('guests'))
        //     ->sortBy('created_at');

        // $guests = $registered->merge($in);
        // $guests->all();

        return view('app.guests.index', compact('guests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = IdentificationType::all(['id', 'type']);

        return view('app.guests.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $guest = new Guest();
        $guest->name = $request->name;
        $guest->last_name = $request->last_name;
        $guest->dni = $request->dni;
        $guest->email = $request->get('email', null);
        $guest->gender = $request->get('gender', null);
        $guest->birthdate = $request->get('birthdate', null);
        $guest->name = $request->get('name', null);
        $guest->status = false; # Not in hotel
        $guest->identificationType()->associate(Id::get($request->type));
        $guest->user()->associate(Id::parent());

        if ($guest->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('guests.show', [
                'id' => Hashids::encode($guest->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('guests.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
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
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Display a listing of searched records.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $status = Input::bool($request->get('status', null));
        $query = Input::clean($request->get('query'));

        $guests = Guest::where('user_id', Id::parent())
            ->where('status', false)
            ->whereLike(['name', 'last_name', 'dni', 'email'], $query)
            ->get(Fields::get('guests'));

        if (!is_null($status)) {
            $guests = $this->filterByStatus($guests, $status);
        }

        if ($request->ajax()) {
            return response()->json([
                'guests' => $this->parseFormat($request, $guests)
            ]);
        } else {
            return response()->json([
                'guests' => empty($guests) ? [] : $guests->toArray()
            ]);
        }
    }

    /**
     * Filter the Guest collection by status.
     *
     * @param Illuminate\Support\Collection  $results
     * @param boolean $status
     * @return Illuminate\Support\Collection
     */
    private function filterByStatus(Collection $results, $status)
    {
        $filtered = $results->filter(function ($result, $key) use ($status) {
            return $result->status == $status;
        });

        return collect($filtered->all());
    }

    /**
     * Parse data results by format request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Illuminate\Support\Collection  $results
     * @return array
     */
    private function parseFormat(Request $request, Collection $results)
    {
        $format = Input::clean($request->get('format', null));
        $template = Input::clean($request->get('template', null));

        if (empty($format)) {
            return $results->toArray();
        }

        if ($format == 'json') {
            return $results->toArray();
        }

        if ($format == 'rendered' and $this->validateTemplate($template)) {
            return $this->renderToTemplate($results, $template);
        }

        return $results->toArray();
    }

    /**
     * Validate if template exists.
     *
     * @param  string  $template
     * @return boolean
     */
    private function validateTemplate($template)
    {
        $templates = [
            'invoices',
        ];

        return in_array($template, $templates);
    }

    /**
     * Render data collection in array.
     *
     * @param Illuminate\Support\Collection  $results
     * @return array
     */
    private function renderToTemplate(Collection $results, $template)
    {
        $rendered = collect();
        $template = 'app.guests.search.' . $template;

        $results->each(function ($guest, $index) use (&$rendered, $template) {
            $render = view($template, compact('guest'))->render();
            $rendered->push($render);
        });

        return $rendered;
    }
}
