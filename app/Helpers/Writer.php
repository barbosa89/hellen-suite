<?php

namespace App\Helpers;

use App\Models\Guest;
use App\Models\Room;
use App\Models\Vehicle;
use App\Models\Voucher;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Writer
{
    private const BLANK = ' ';

    private const COMMA = ', ';

    /**
     * The writte
     *
     * @var string
     */
    private string $writte = '';

    /**
     * Return a link tag
     *
     * @param Voucher $voucher
     * @return string
     */
    private function buildLink(Voucher $voucher): string
    {
        $url = route('vouchers.show', ['id' => $voucher->hash]);

        return "<a href='{$url}' target='_blank' rel='noopener noreferrer'>{$voucher->number}</a>";
    }

    /**
     * Build text for guest check in
     *
     * @param \App\Models\Voucher $voucher
     * @return \App\Helpers\Writer
     */
    public function checkin(Voucher $voucher): Writer
    {
        // Get link to show voucher
        $link = $this->buildLink($voucher);

        // Replace link in placeholder
        $text = str_replace('{link}', $link, trans('notes.checkin.of'));

        // Add text
        $this->writte .= $text . self::BLANK;

        return $this;
    }

    /**
     * Build text for guest check out
     *
     * @param \App\Models\Voucher $voucher
     * @return \App\Helpers\Writer
     */
    public function checkout(Voucher $voucher): Writer
    {
        // Get link to show voucher
        $link = $this->buildLink($voucher);

        // Replace link in placeholder
        $text = str_replace('{link}', $link, trans('notes.checkout.of'));

        // Add text
        $this->writte .= $text . self::BLANK;

        return $this;
    }

    /**
     * Build text about guest personal data
     *
     * @param \App\Models\Guest $guest
     * @return \App\Helpers\Writer
     */
    public function guest(Guest $guest): Writer
    {
        $idType = strtoupper($guest->identificationType->type);

        $text = "{$guest->full_name} {$idType} {$guest->dni}," . self::BLANK;

        $this->writte .= $text;

        return $this;
    }

    /**
     * Build text for each guest about personal data and assigned hotel room
     *
     * @param \Illuminate\Support\Collection $guests
     * @return \App\Helpers\Writer
     */
    public function guests(Collection $guests): Writer
    {
        $text = '';
        foreach ($guests as $guest) {
            $room = lcfirst(trans('rooms.number', ['number' => $guest->rooms->first()->number])) . self::COMMA;

            $idType = strtoupper($guest->identificationType->type);

            $text .= "{$guest->full_name} {$idType} {$guest->dni}, {$room}";
        }

        $this->writte .= $text;

        return $this;
    }

    /**
     * Build text about assigned hotel room
     *
     * @param \App\Models\\App\Models\Room $room
     * @return \App\Helpers\Writer
     */
    public function room(Room $room): Writer
    {
        $text = lcfirst(trans('rooms.number', ['number' => $room->number])) . self::COMMA;

        $this->writte .= $text;

        return $this;
    }

    /**
     * Build text about vehicle entry
     *
     * @param \App\Models\Voucher $voucher
     * @param \App\Models\Vehicle $vehicle
     * @return \App\Helpers\Writer
     */
    public function vehicle(Voucher $voucher, Vehicle $vehicle): Writer
    {
        // Get link to show voucher
        $link = $this->buildLink($voucher);

        // Replace link in placeholder
        $text = str_replace('{link}', $link, trans('notes.vehicle'));

        $type = trans('vehicles.' . $vehicle->type->type);

        // Add vehicle registration, vehicle type
        $text .= " {$vehicle->registration}, {$type}";
        $text .= self::COMMA;

        $this->writte .= $text;

        return $this;
    }

    /**
     * Build text about vehicle owner
     * This method wraps Writer->guest() method
     *
     * @param \App\Models\Guest $guest
     * @return \App\Helpers\Writer
     */
    public function owner(Guest $guest): Writer
    {
        $this->writte .= trans('notes.owner') . self::BLANK;

        return $this->guest($guest);
    }

    /**
     * Return the writte
     *
     * @return string
     */
    public function write(): string
    {
        return Str::of($this->writte)->trim()->replaceLast(',', '.');
    }
}
