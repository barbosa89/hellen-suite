<?php

declare(strict_types=1);

namespace Tests\Feature\Payments;

use App\Actions\Payments\BuildPaymentVoucherData;
use App\Actions\Stays\CreateStayFolio;
use App\Constants\PaymentMethod;
use App\Constants\PaymentVoucherFormat;
use App\Models\FolioCharge;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomOccupancy;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\StayFolio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentVoucherTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function vouchers_are_numbered_sequentially_per_hotel(): void
    {
        $hotel = Hotel::factory()->create();
        [$firstStay, $firstFolio] = $this->folio($hotel);
        [$secondStay, $secondFolio] = $this->folio($hotel);
        [$otherStay, $otherFolio] = $this->folio();

        $firstPayment = $this->recordPayment($hotel, $firstStay, $firstFolio);
        $secondPayment = $this->recordPayment($hotel, $secondStay, $secondFolio);
        $otherPayment = $this->recordPayment($otherFolio->hotel, $otherStay, $otherFolio);

        $this->assertSame(1, $firstPayment->voucher->number);
        $this->assertSame(2, $secondPayment->voucher->number);
        $this->assertSame(1, $otherPayment->voucher->number);
    }

    #[Test]
    public function voucher_cannot_be_printed_while_the_stay_is_active(): void
    {
        [$stay, $folio] = $this->folio();
        $payment = $this->recordPayment($folio->hotel, $stay, $folio);

        $this->get($this->voucherUrl($folio->hotel, $stay, $payment, PaymentVoucherFormat::A4))
            ->assertNotFound();
    }

    #[Test]
    public function closed_stay_voucher_can_be_streamed_in_both_formats(): void
    {
        [$stay, $folio] = $this->folio();
        $payment = $this->recordPayment($folio->hotel, $stay, $folio);

        $this->post(route('hotels.stays.check-out', [$folio->hotel, $stay]))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        foreach (PaymentVoucherFormat::cases() as $format) {
            $response = $this->get($this->voucherUrl($folio->hotel, $stay, $payment, $format));

            $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
            $this->assertStringContainsString('inline;', (string) $response->headers->get('Content-Disposition'));
            $this->assertStringStartsWith('%PDF', (string) $response->getContent());
        }
    }

    #[Test]
    public function voucher_is_scoped_to_its_hotel_and_stay(): void
    {
        [$stay, $folio] = $this->folio();
        $payment = $this->recordPayment($folio->hotel, $stay, $folio);
        $this->post(route('hotels.stays.check-out', [$folio->hotel, $stay]))
            ->assertSessionHasNoErrors();

        [$otherStay, $otherFolio] = $this->folio();
        $otherPayment = $this->recordPayment($otherFolio->hotel, $otherStay, $otherFolio);
        $this->post(route('hotels.stays.check-out', [$otherFolio->hotel, $otherStay]));

        $this->get($this->voucherUrl($folio->hotel, $stay, $otherPayment, PaymentVoucherFormat::A4))
            ->assertNotFound();
        $this->get($this->voucherUrl($otherFolio->hotel, $otherStay, $payment, PaymentVoucherFormat::A4))
            ->assertNotFound();
    }

    #[Test]
    public function voucher_uses_current_hotel_and_responsible_guest_data(): void
    {
        [$stay, $folio, $guest] = $this->folio();
        $payment = $this->recordPayment($folio->hotel, $stay, $folio);
        $secondRoomType = RoomType::factory()->for($folio->hotel)->create();
        $secondRoom = Room::factory()->for($folio->hotel)->for($secondRoomType)->create();
        $secondOccupancy = RoomOccupancy::factory()->for($stay)->for($secondRoom)->create([
            'nightly_rate' => '1000.00',
            'checked_in_at' => now()->subDay(),
            'expected_check_out_on' => today()->toDateString(),
        ]);
        $secondFolio = app(CreateStayFolio::class)->execute($secondOccupancy);
        $this->recordPayment($folio->hotel, $stay, $secondFolio);

        $this->post(route('hotels.stays.check-out', [$folio->hotel, $stay]))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $folio->hotel->update(['business_name' => 'Hotel Current Name']);
        $guest->update(['first_name' => 'Current', 'last_name' => 'Guest']);

        $this->assertSame(2, FolioCharge::query()->count());

        $voucher = app(BuildPaymentVoucherData::class)->execute($payment->refresh());

        $this->assertSame('Hotel Current Name', $voucher['hotel']['business_name']);
        $this->assertSame('Current Guest', $voucher['responsible']['name']);
        $this->assertSame('CP-000001', $voucher['code']);
        $this->assertCount(2, $voucher['folios']);
        $this->assertCount(1, $voucher['folios'][0]['charges']);
        $this->assertCount(1, $voucher['folios'][1]['charges']);
        $this->assertSame(0, $voucher['folios'][0]['projected_charge_minor']);
    }

    #[Test]
    public function invalid_voucher_format_returns_not_found(): void
    {
        [$stay, $folio] = $this->folio();
        $payment = $this->recordPayment($folio->hotel, $stay, $folio);
        $this->post(route('hotels.stays.check-out', [$folio->hotel, $stay]));

        $this->get(route('hotels.stays.payments.voucher', [$folio->hotel, $stay, $payment, 'letter']))
            ->assertNotFound();
    }

    private function recordPayment(Hotel $hotel, Stay $stay, StayFolio $folio): Payment
    {
        $this->post(route('hotels.stays.folios.payments.store', [$hotel, $stay, $folio]), [
            'amount' => '1000.00',
            'method' => PaymentMethod::Cash->value,
        ])->assertSessionHasNoErrors();

        return $folio->payments()->with('voucher')->sole();
    }

    /** @return array{Stay, StayFolio, Guest} */
    private function folio(null|Hotel $hotel = null): array
    {
        $hotel ??= Hotel::factory()->create(['image' => null]);
        $guest = Guest::factory()->for($hotel)->create();
        $stay = Stay::factory()->for($hotel)->create([
            'responsible_guest_id' => $guest->id,
            'checked_in_at' => now()->subDay(),
            'expected_check_out_on' => today()->toDateString(),
        ]);
        $roomType = RoomType::factory()->for($hotel)->create();
        $room = Room::factory()->for($hotel)->for($roomType)->create();
        $occupancy = RoomOccupancy::factory()->for($stay)->for($room)->create([
            'nightly_rate' => '1000.00',
            'checked_in_at' => now()->subDay(),
            'expected_check_out_on' => today()->toDateString(),
        ]);

        return [$stay, app(CreateStayFolio::class)->execute($occupancy), $guest];
    }

    private function voucherUrl(Hotel $hotel, Stay $stay, Payment $payment, PaymentVoucherFormat $format): string
    {
        return route('hotels.stays.payments.voucher', [$hotel, $stay, $payment, $format]);
    }
}
