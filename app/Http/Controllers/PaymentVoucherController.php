<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Payments\BuildPaymentVoucherData;
use App\Constants\PaymentType;
use App\Constants\PaymentVoucherFormat;
use App\Constants\StayStatus;
use App\Models\Hotel;
use App\Models\Payment;
use App\Models\Stay;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class PaymentVoucherController extends Controller
{
    public function __invoke(Hotel $hotel, Stay $stay, Payment $payment, PaymentVoucherFormat $format, BuildPaymentVoucherData $buildPaymentVoucherData): Response
    {
        abort_unless($stay->status === StayStatus::CheckedOut, 404);
        abort_if($stay->folios()->whereNull('closed_at')->exists(), 404);

        $payment = Payment::query()
            ->where('type', PaymentType::Receipt)
            ->whereHas('voucher')
            ->whereHas(
                'folio',
                fn (Builder $query): Builder => $query->where('hotel_id', $hotel->id)
                    ->where('stay_id', $stay->id)
            )
            ->findOrFail($payment->id);

        $voucher = $buildPaymentVoucherData->execute($payment);
        $pdf = Pdf::loadView("payments.vouchers.{$format->value}", ['voucher' => $voucher]);

        if ($format === PaymentVoucherFormat::Thermal) {
            $pdf->setPaper([0, 0, 226.77, 841.89]);
        } else {
            $pdf->setPaper('a4', 'portrait');
        }

        return $pdf->stream("comprobante-{$voucher['code']}-{$format->value}.pdf");
    }
}
