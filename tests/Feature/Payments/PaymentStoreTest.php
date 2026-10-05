<?php

declare(strict_types=1);

namespace Tests\Feature\Payments;

use App\Actions\Stays\CreateStayFolio;
use App\Constants\CashMovementDirection;
use App\Constants\CashMovementType;
use App\Constants\PaymentMethod;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomOccupancy;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\StayFolio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentStoreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function cash_payment_creates_one_cash_entry(): void
    {
        [$hotel, $stay, $folio] = $this->folio();

        $this->post(route('hotels.stays.folios.payments.store', [$hotel, $stay, $folio]), [
            'amount' => '1000.00',
            'method' => PaymentMethod::Cash->value,
            'comment' => 'Paid at reception',
        ])->assertSessionHasNoErrors();

        $payment = $folio->payments()->sole();
        $movement = $hotel->cashMovements()->sole();

        $this->assertSame(100000, $payment->amount_minor);
        $this->assertSame(PaymentMethod::Cash, $payment->method);
        $this->assertSame(1, $payment->voucher->number);
        $this->assertSame($payment->id, $movement->payment_id);
        $this->assertSame(CashMovementDirection::In, $movement->direction);
    }

    #[Test]
    public function bank_transfer_can_store_evidence_without_moving_cash(): void
    {
        Storage::fake('public');

        [$hotel, $stay, $folio] = $this->folio();

        $this->post(route('hotels.stays.folios.payments.store', [$hotel, $stay, $folio]), [
            'amount' => '1000.00',
            'method' => PaymentMethod::BankTransfer->value,
            'support' => UploadedFile::fake()->image('transfer.webp'),
        ])->assertSessionHasNoErrors();

        $payment = $folio->payments()->sole();

        $this->assertSame(PaymentMethod::BankTransfer, $payment->method);

        Storage::disk('public')->assertExists($payment->getRawOriginal('support_path'));

        $this->assertSame(0, $hotel->cashMovements()->count());
    }

    #[Test]
    public function payment_cannot_exceed_the_projected_balance(): void
    {
        [$hotel, $stay, $folio] = $this->folio();

        $this->post(route('hotels.stays.folios.payments.store', [$hotel, $stay, $folio]), [
            'amount' => '1000.01',
            'method' => PaymentMethod::Cash->value,
        ])->assertSessionHasErrors('amount');

        $this->assertSame(0, $folio->payments()->count());
        $this->assertSame(0, $hotel->cashMovements()->count());
    }

    #[Test]
    public function payment_cannot_be_refunded_after_the_folio_is_closed(): void
    {
        [$hotel, $stay, $folio] = $this->folio();

        $this->post(route('hotels.stays.folios.payments.store', [$hotel, $stay, $folio]), [
            'amount' => '1000.00',
            'method' => PaymentMethod::Cash->value,
        ])->assertSessionHasNoErrors();

        $payment = $folio->payments()->sole();
        $folio->update(['closed_at' => now()]);

        $this->post(route('hotels.stays.payments.refund', [$hotel, $stay, $payment]), [
            'amount' => '100.00',
            'reason' => 'Requested after checkout',
        ])->assertSessionHasErrors('payment');

        $this->assertSame(1, $folio->payments()->count());
        $this->assertSame(1, $hotel->cashMovements()->count());
    }

    #[Test]
    public function cash_payment_cannot_be_refunded_without_available_cash(): void
    {
        [$hotel, $stay, $folio] = $this->folio();

        $this->post(route('hotels.stays.folios.payments.store', [$hotel, $stay, $folio]), [
            'amount' => '1000.00',
            'method' => PaymentMethod::Cash->value,
        ])->assertSessionHasNoErrors();

        $payment = $folio->payments()->sole();
        $hotel->cashMovements()->create([
            'type' => CashMovementType::Withdrawal,
            'direction' => CashMovementDirection::Out,
            'amount_minor' => 100000,
            'currency' => $folio->currency,
            'comment' => 'Cash removed',
            'occurred_at' => now(),
            'idempotency_key' => fake()->uuid(),
            'cash_shift_id' => $hotel->cashShifts()->whereNull('closed_at')->value('id'),
        ]);

        $this->post(route('hotels.stays.payments.refund', [$hotel, $stay, $payment]), [
            'amount' => '100.00',
            'reason' => 'No cash available',
        ])->assertSessionHasErrors('amount');

        $this->assertSame(1, $folio->payments()->count());
    }

    /** @return array{Hotel, Stay, StayFolio} */
    private function folio(): array
    {
        $hotel = Hotel::factory()->create();

        $this->post(route('hotels.cash.shifts.store', $hotel), ['opening_amount' => '0.00'])
            ->assertSessionHasNoErrors();

            $guest = Guest::factory()->for($hotel)->create();
        $stay = Stay::factory()->for($hotel)->create(['responsible_guest_id' => $guest->id]);
        $roomType = RoomType::factory()->for($hotel)->create();
        $room = Room::factory()->for($hotel)->for($roomType)->create();
        $occupancy = RoomOccupancy::factory()->for($stay)->for($room)->create(['nightly_rate' => '1000.00']);

        return [$hotel, $stay, app(CreateStayFolio::class)->execute($occupancy)];
    }
}
