<?php

declare(strict_types=1);

namespace Tests\Feature\Cash;

use App\Constants\CashMovementType;
use App\Constants\CashShiftReportFormat;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CashShiftTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_hotel_can_only_have_one_open_shift(): void
    {
        $hotel = Hotel::factory()->create();

        $this->openShift($hotel, '250.00');

        $this->post(route('hotels.cash.shifts.store', $hotel), [
            'opening_amount' => '100.00',
        ])->assertSessionHasErrors('cash_shift');

        $shift = $hotel->cashShifts()->with('reconciliations')->sole();
        $this->assertSame(1, $shift->number);
        $this->assertSame(25000, $shift->reconciliations->sole()->opening_minor);
    }

    #[Test]
    public function financial_operations_require_an_open_shift(): void
    {
        $hotel = Hotel::factory()->create();

        $this->post(route('hotels.cash.store', $hotel), [
            'type' => CashMovementType::ManualEntry->value,
            'amount' => '25.00',
            'comment' => 'Without shift',
        ])->assertSessionHasErrors('cash_shift');

        $this->assertDatabaseCount('cash_movements', 0);
    }

    #[Test]
    public function a_shift_closes_with_an_immutable_reconciliation(): void
    {
        $hotel = Hotel::factory()->create();
        $this->openShift($hotel, '100.00');
        $shift = $hotel->cashShifts()->sole();

        $this->post(route('hotels.cash.store', $hotel), [
            'type' => CashMovementType::ManualEntry->value,
            'amount' => '50.00',
            'comment' => 'Additional cash',
        ])->assertSessionHasNoErrors();

        $this->post(route('hotels.cash.store', $hotel), [
            'type' => CashMovementType::Withdrawal->value,
            'amount' => '20.00',
            'comment' => 'Supplies',
        ])->assertSessionHasNoErrors();

        $this->post(route('hotels.cash.shifts.close', [$hotel, $shift]), [
            'reconciliations' => [[
                'method' => 'cash',
                'currency' => 'COP',
                'declared_amount' => '128.00',
            ]],
            'closing_note' => 'Two pesos short',
        ])->assertSessionHasNoErrors();

        $reconciliation = $shift->reconciliations()->sole();
        $this->assertSame(10000, $reconciliation->opening_minor);
        $this->assertSame(5000, $reconciliation->inflow_minor);
        $this->assertSame(2000, $reconciliation->outflow_minor);
        $this->assertSame(13000, $reconciliation->expected_closing_minor);
        $this->assertSame(12800, $reconciliation->declared_closing_minor);
        $this->assertSame(-200, $reconciliation->difference_minor);
        $this->assertNotNull($shift->fresh()->closed_at);

        $this->post(route('hotels.cash.shifts.close', [$hotel, $shift]), [
            'reconciliations' => [[
                'method' => 'cash',
                'currency' => 'COP',
                'declared_amount' => '130.00',
            ]],
        ])->assertSessionHasErrors('cash_shift');
    }

    #[Test]
    public function handing_over_closes_the_current_shift_and_opens_the_next(): void
    {
        $hotel = Hotel::factory()->create();
        $this->openShift($hotel, '100.00');
        $firstShift = $hotel->cashShifts()->sole();

        $this->post(route('hotels.cash.shifts.handover', [$hotel, $firstShift]), [
            'reconciliations' => [[
                'method' => 'cash',
                'currency' => 'COP',
                'declared_amount' => '95.00',
            ]],
            'opening_note' => 'Carried from prior shift',
        ])->assertRedirect(route('hotels.cash.index', $hotel))->assertSessionHasNoErrors();

        $secondShift = $hotel->cashShifts()->whereNull('closed_at')->with('reconciliations')->sole();
        $this->assertNotNull($firstShift->fresh()->closed_at);
        $this->assertSame(2, $secondShift->number);
        $this->assertSame($firstShift->id, $secondShift->previous_cash_shift_id);
        $this->assertSame(9500, $secondShift->reconciliations->sole()->opening_minor);
    }

    #[Test]
    public function a_closed_shift_report_is_available_in_both_formats(): void
    {
        $hotel = Hotel::factory()->create();
        $this->openShift($hotel, '100.00');
        $shift = $hotel->cashShifts()->sole();
        $this->post(route('hotels.cash.shifts.close', [$hotel, $shift]), [
            'reconciliations' => [['method' => 'cash', 'currency' => 'COP', 'declared_amount' => '100.00']],
        ])->assertSessionHasNoErrors();

        foreach (CashShiftReportFormat::cases() as $format) {
            $this->get(route('hotels.cash.shifts.report', [$hotel, $shift, $format]))
                ->assertOk()
                ->assertHeader('Content-Type', 'application/pdf');
        }
    }

    private function openShift(Hotel $hotel, string $amount): void
    {
        $this->post(route('hotels.cash.shifts.store', $hotel), [
            'opening_amount' => $amount,
        ])->assertSessionHasNoErrors();
    }
}
