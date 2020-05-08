<?php

namespace App\Repository;

use App\Repository\Repository;
use App\Welkome\Note;
use App\Welkome\Shift;
use Illuminate\Http\Request;

/**
 * Pure Eloquent Repository
 */
class NoteRepository implements Repository
{
    /**
     * Create new Note
     *
     * @param   \Illuminate\Http\Request $request
     * @return  \App\Welkome\Note
     */
    public function create(Request $request): Note
    {
        $note = new Note();
        $note->content = $request->content;
        $note->team_member_name = auth()->user()->name;
        $note->team_member_email = auth()->user()->email;
        $note->hotel()->associate(id_decode($request->hotel));
        $note->user()->associate(id_parent());
        $note->saveOrFail();

        // Get all Tag Ids
        $ids = collect($request->tags)->pluck('hash')->toArray();
        $note->tags()->sync(id_decode_recursive($ids));

        // If add is true
        // The note is attached to Shift
        if ($request->add) {
            Shift::current(id_decode($request->hotel))
                ->notes()
                ->attach($note);
        }

        return $note;
    }

    /**
     * Retrieve model by ID
     *
     * @param  integer $id
     * @return \App\Welkome\Note
     */
    public function get(int $id): Note
    {
        $note = Note::whereUserId(id_parent())
            ->whereId($id)
            ->get(Note::getColumnNames());

        return $note;
    }

    /**
     * Update model
     *
     * @param  Illuminate\Http\Request $request
     * @param  integer $id
     * @return \App\Welkome\Note
     */
    public function update(Request $request, int $id): Note
    {
        $note = $this->get($id);
        $note->content = $request->content;
        $note->saveOrFail();

        return $note;
    }

    /**
     * Destroy model
     *
     * @param integer $id
     * @return boolean
     */
    public function destroy(int $id): bool
    {
        $note = $this->get($id);

        return $note->delete();
    }
}
