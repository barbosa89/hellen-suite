<?php

namespace App\Helpers;

use App\Welkome\Guest;
use App\Welkome\Room;
use App\Welkome\Vehicle;
use App\Welkome\Voucher;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Writer
{
    /**
     * Blank space
     *
     * @var string
     */
    private const BLANK = ' ';

    /**
     * The writte
     *
     * @var string
     */
    private string $writte = '';

    /**
     * Return a link tag
     *
     * @param string $url
     * @param string $text
     * @return string
     */
    private function buildLink(string $url, string $text): string
    {
        return "<a href='{$url}' target='_blank'>{$text}</a>";
    }

    /**
     * Build text for guest check in
     *
     * @param \App\Welkome\Voucher $voucher
     * @return \App\Helpers\Writer
     */
    public function checkin(Voucher $voucher): Writer
    {
        // Get link to show voucher
        $link = $this->buildLink(route('vouchers.show', ['id' => id_encode($voucher->id)]), $voucher->number);

        // Replace link in placeholder
        $text = str_replace('{link}', $link, trans('notes.checkin.of'));

        // Add text
        $this->writte .= $text . self::BLANK;

        return $this;
    }

    /**
     * Build text for guest check out
     *
     * @param \App\Welkome\Voucher $voucher
     * @return \App\Helpers\Writer
     */
    public function checkout(Voucher $voucher): Writer
    {
        // Get link to show voucher
        $link = $this->buildLink(route('vouchers.show', ['id' => id_encode($voucher->id)]), $voucher->number);

        // Replace link in placeholder
        $text = str_replace('{link}', $link, trans('notes.checkout.of'));

        // Add text
        $this->writte .= $text . self::BLANK;

        return $this;
    }

    /**
     * Build text about guest personal data
     *
     * @param \App\Welkome\Guest $guest
     * @return \App\Helpers\Writer
     */
    public function guest(Guest $guest): Writer
    {
        $idType = strtoupper($guest->identificationType->type);

        $text = "{$guest->full_name} {$idType} {$guest->dni}, ";

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
            $room = strtolower(trans('rooms.room')) . " No. {$guest->rooms->first()->number}, ";

            $idType = strtoupper($guest->identificationType->type);

            $text .= "{$guest->full_name} {$idType} {$guest->dni}, {$room}";
        }

        $this->writte .= $text;

        return $this;
    }

    /**
     * Build text about assigned hotel room
     *
     * @param \App\Welkome\\App\Welkome\Room $room
     * @return \App\Helpers\Writer
     */
    public function room(Room $room): Writer
    {
        $text = strtolower(trans('rooms.room')) . " No. {$room->number}, ";

        $this->writte .= $text;

        return $this;
    }

    /**
     * Build text about vehicle entry
     *
     * @param \App\Welkome\Voucher $voucher
     * @param \App\Welkome\Vehicle $vehicle
     * @return \App\Helpers\Writer
     */
    public function vehicle(Voucher $voucher, Vehicle $vehicle): Writer
    {
        // Get link to show voucher
        $link = $this->buildLink(route('vouchers.show', ['id' => id_encode($voucher->id)]), $voucher->number);

        // Replace link in placeholder
        $text = str_replace('{link}', $link, trans('notes.vehicle'));

        $type = trans('vehicles.' . $vehicle->type->type);

        // Add vehicle registration, vehicle type
        $text .= " {$vehicle->registration}, {$type}, ";

        $this->writte .= $text;

        return $this;
    }

    /**
     * Build text about vehicle owner
     * This method wraps Writer->guest() method
     *
     * @param \App\Welkome\Guest $guest
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
