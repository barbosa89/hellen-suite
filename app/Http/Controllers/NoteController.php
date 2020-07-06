<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNote;
use App\Repositories\NoteRepository;
use App\Welkome\Hotel;
use App\Welkome\Note;
use App\Welkome\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;

class NoteController extends Controller
{
    /**
     * Note repository Eloquent based
     *
     * @var NoteRepository
     */
    public NoteRepository $note;

    /**
     * Construct function
     *
     * @param \App\Repositories\NoteRepository $note
     */
    public function __construct(NoteRepository $note)
    {
        $this->note = $note;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('app.notes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app.notes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNote $request)
    {
        $note = $this->note->create($request->validated());

        // If add is true, the note is attached to Shift
        if ($request->add) {
            Shift::current(id_decode($request->hotel_id))
                ->notes()
                ->attach($note);
        }

        return response()->json([
            'status' => $note instanceof Note
        ]);
    }

    /**
     * Search notes between dates and hotel.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $validator = $this->validation($request->toArray());

        if ($validator->fails()) {
            return redirect(url()->previous())
                        ->withErrors($validator)
                        ->withInput();
        }

        $start = param_clean($request->start);
        $end = param_clean($request->end);
        $text = param_clean($request->get('query', null));

        $hotel = Hotel::whereUserId(id_parent())
            ->whereId(id_decode($request->hotel))
            ->first(['id', 'business_name']);

        $notes = $this->note->search(id_decode($request->hotel), $start, $end, $text);

        return view('app.notes.search', compact('notes', 'start', 'end', 'hotel', 'text'));
    }


    /**
     * Export the notes between dates and hotel in PDF format.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $validator = $this->validation($request->toArray());

        if ($validator->fails()) {
            return redirect(url()->previous())
                        ->withErrors($validator)
                        ->withInput();
        }

        $start = param_clean($request->start);
        $end = param_clean($request->end);
        $text = param_clean($request->get('query', null));

        $hotel = Hotel::whereUserId(id_parent())
            ->whereId(id_decode($request->hotel))
            ->first(['id', 'business_name']);

        $notes = $this->note->list(id_decode($request->hotel), $start, $end, $text);

        $view = view('app.notes.exports.template', compact('notes', 'hotel'))->render();

        $pdf = get_pdf_printer([5, 5, 6, 6]);
        $pdf->loadHTML($view);

        return $pdf->download(trans('notes.title') . '.pdf');
    }

    /**
     * Validate data query
     *
     * @param array $data
     * @return \Illuminate\Validation\ValidationValidator
     */
    public function validation(array $data = null): ValidationValidator
    {
        return Validator::make($data, [
            'hotel' => 'required|string|hashed_exists:hotels,id',
            'start' => 'required|date|before_or_equal:today',
            'end' => 'required|date|after_or_equal:start|before_or_equal:today'
        ]);
    }
}
