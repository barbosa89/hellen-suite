<?php

declare(strict_types=1);

namespace Tests\Feature\Cash;

use App\Constants\CashMovementDirection;
use App\Constants\CashMovementType;
use App\Models\CashMovement;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CashMovementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_records_manual_entries_and_withdrawals(): void
    {
        $hotel = Hotel::factory()->create();
        $this->openShift($hotel, '100.00');

        $this->post(route('hotels.cash.store', $hotel), [
            'type' => CashMovementType::ManualEntry->value,
            'amount' => '500.00',
            'comment' => 'Opening cash',
        ])->assertSessionHasNoErrors();

        $this->post(route('hotels.cash.store', $hotel), [
            'type' => CashMovementType::Withdrawal->value,
            'amount' => '125.00',
            'comment' => 'Petty cash',
        ])->assertSessionHasNoErrors();

        $this->assertSame(CashMovementDirection::In, $hotel->cashMovements()->oldest('id')->firstOrFail()->direction);
        $this->assertSame(CashMovementDirection::Out, $hotel->cashMovements()->latest('id')->firstOrFail()->direction);
    }

    #[Test]
    public function withdrawal_cannot_exceed_available_cash(): void
    {
        $hotel = Hotel::factory()->create();
        $this->openShift($hotel, '100.00');

        $this->post(route('hotels.cash.store', $hotel), [
            'type' => CashMovementType::Withdrawal->value,
            'amount' => '100.01',
            'comment' => 'Too much',
        ])->assertSessionHasErrors('amount');

        $this->assertSame(0, $hotel->cashMovements()->count());
    }

    #[Test]
    public function withdrawal_ignores_cash_recorded_in_another_currency(): void
    {
        $hotel = Hotel::factory()->create();
        $this->openShift($hotel, '0.00');
        CashMovement::factory()->for($hotel)->create([
            'amount_minor' => 10000,
            'currency' => 'USD',
        ]);

        $this->post(route('hotels.cash.store', $hotel), [
            'type' => CashMovementType::Withdrawal->value,
            'amount' => '1.00',
            'comment' => 'Wrong currency',
        ])->assertSessionHasErrors('amount');

        $this->assertSame(1, $hotel->cashMovements()->count());
    }

    private function openShift(Hotel $hotel, string $amount): void
    {
        $this->post(route('hotels.cash.shifts.store', $hotel), [
            'opening_amount' => $amount,
        ])->assertSessionHasNoErrors();
    }
}
