<?php

namespace App\Helpers;

use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Note;
use App\Models\Room;
use App\Models\Tag;
use App\Models\Vehicle;
use App\Models\Voucher;

class Notary
{
    private Hotel $hotel;

    private Writer $writer;

    /**
     * Construct function
     */
    public function __construct(Hotel $hotel)
    {
        $this->hotel = $hotel;
        $this->writer = new Writer;

    }

    /**
     * Create note for guest check in
     */
    public function checkinGuest(Voucher $voucher, Guest $guest, Room $room): void
    {
        $this->writer->checkin($voucher)
            ->guest($guest)
            ->room($room);

        $this->createNote(Tag::CHECK_IN);
    }

    /**
     * Create note for guest check out
     */
    public function checkoutGuest(Voucher $voucher, Guest $guest, Room $room): void
    {
        $this->writer->checkout($voucher)
            ->guest($guest)
            ->room($room);

        $this->createNote(Tag::CHECK_OUT);
    }

    /**
     * Create note for check out of many guests
     */
    public function checkoutGuests(Voucher $voucher): void
    {
        $this->writer->checkout($voucher)
            ->guests($voucher->guests);

        $this->createNote(Tag::CHECK_OUT);
    }

    /**
     * Create note for vehicle entry
     *
     * @return void
     */
    public function vehicleEntry(Voucher $voucher, Guest $guest, Vehicle $vehicle)
    {
        $this->writer->vehicle($voucher, $vehicle)
            ->owner($guest);

        $this->createNote(Tag::VEHICLE);
    }

    /**
     * Store new note
     */
    private function createNote(string $tag): void
    {
        $note = new Note;
        $note->content = $this->writer->write();
        $note->team_member_name = auth()->user()->name;
        $note->team_member_email = auth()->user()->email;
        $note->hotel()->associate($this->hotel->id);
        $note->user()->associate(id_parent());
        $note->save();

        // Attach tag
        $note->tags()->attach($this->getTag($tag)->id);
    }

    /**
     * Return a existing Tag or create Tag
     */
    private function getTag(string $tag): Tag
    {
        $tag = Tag::firstOrNew([
            'description' => $tag,
        ]);

        $tag->user()->associate(id_parent());
        $tag->saveOrFail();

        return $tag;
    }

    /**
     * Create new Notary object
     */
    public static function create(Hotel $hotel): Notary
    {
        return new Notary($hotel);
    }
}
