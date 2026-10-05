<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Cash\BuildCashShiftReportData;
use App\Constants\CashShiftReportFormat;
use App\Models\CashShift;
use App\Models\Hotel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class CashShiftReportController extends Controller
{
    public function __invoke(Hotel $hotel, CashShift $cashShift, CashShiftReportFormat $format, BuildCashShiftReportData $buildReport): Response
    {
        abort_unless($cashShift->hotel_id === $hotel->id && $cashShift->closed_at !== null, Response::HTTP_NOT_FOUND);

        $report = $buildReport->execute($cashShift);
        $pdf = Pdf::loadView("cash.shifts.{$format->value}", ['report' => $report]);

        $format === CashShiftReportFormat::Thermal
            ? $pdf->setPaper([0, 0, 226.77, 600])
            : $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("turno-{$report['code']}-{$format->value}.pdf");
    }
}
