<?php

declare(strict_types=1);

namespace Tests\Feature\Tra;

use App\Constants\IdentificationTypeCode;
use App\Constants\TraSubmissionKind;
use App\Models\IdentificationType;
use App\Services\Tra\TraPayloadMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TraPayloadMapperTest extends TestCase
{
    use InteractsWithTra;
    use RefreshDatabase;

    #[Test]
    public function it_maps_the_principal_payload_like_the_guide(): void
    {
        ['hotel' => $hotel, 'stay' => $stay, 'occupancy' => $occupancy, 'principal' => $principal] = $this->traStay();

        $stay->load(['roomOccupancies.room', 'roomOccupancies.guests', 'stayGuests.guest.identificationType']);
        $occupancy->load(['room', 'guests']);

        $payload = (new TraPayloadMapper())->map($hotel, $stay, $occupancy, $principal, TraSubmissionKind::Principal);

        $this->assertEquals([
            'rnt' => '12345',
            'token' => 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6',
            'tipo_documento' => 'CC',
            'numero_documento' => '1020304050',
            'primer_nombre' => 'JUAN',
            'segundo_nombre' => 'CARLOS',
            'primer_apellido' => 'PEREZ',
            'segundo_apellido' => 'GOMEZ',
            'fecha_nacimiento' => '1985-05-20',
            'genero' => 'M',
            'nacionalidad' => 'COL',
            'pais_residencia' => 'COL',
            'departamento_residencia' => '11',
            'municipio_residencia' => '11001',
            'pais_procedencia' => 'COL',
            'departamento_procedencia' => '05',
            'municipio_procedencia' => '05001',
            'pais_destino' => 'COL',
            'departamento_destino' => '11',
            'municipio_destino' => '11001',
            'motivo_viaje' => '01',
            'medio_transporte' => '02',
            'fecha_entrada' => '2026-10-06',
            'fecha_salida' => '2026-10-10',
            'numero_habitacion' => '301',
            'tarifa_habitacion' => 180000,
            'numero_acompanantes' => 1,
        ], $payload);
    }

    #[Test]
    public function it_maps_the_companion_payload_like_the_guide(): void
    {
        ['hotel' => $hotel, 'stay' => $stay, 'occupancy' => $occupancy, 'companion' => $companion] = $this->traStay();

        $stay->load(['roomOccupancies.room', 'roomOccupancies.guests', 'stayGuests.guest.identificationType']);
        $occupancy->load(['room', 'guests']);

        $payload = (new TraPayloadMapper())->map($hotel, $stay, $occupancy, $companion, TraSubmissionKind::Companion);

        $this->assertEquals([
            'rnt' => '12345',
            'token' => 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6',
            'documento_principal' => '1020304050',
            'tipo_documento' => 'CE',
            'numero_documento' => '987654321',
            'primer_nombre' => 'MARIA',
            'primer_apellido' => 'SILVA',
            'fecha_nacimiento' => '1990-08-12',
            'genero' => 'F',
            'nacionalidad' => 'BRA',
            'pais_residencia' => 'BRA',
            'pais_procedencia' => 'BRA',
            'pais_destino' => 'COL',
            'departamento_destino' => '11',
            'municipio_destino' => '11001',
            'motivo_viaje' => '01',
            'medio_transporte' => '01',
            'fecha_entrada' => '2026-10-06',
            'fecha_salida' => '2026-10-10',
            'numero_habitacion' => '301',
        ], $payload);
    }

    #[Test]
    public function it_reports_missing_data_instead_of_guessing(): void
    {
        ['hotel' => $hotel, 'stay' => $stay, 'occupancy' => $occupancy, 'principal' => $principal] = $this->traStay();

        $stay->load(['roomOccupancies.room', 'roomOccupancies.guests', 'stayGuests.guest.identificationType']);

        $principal->guest->update(['birth_date' => null]);
        $principal->update(['origin_locality' => null]);

        $dni = IdentificationType::factory()->create(['code' => IdentificationTypeCode::ForeignNationalId]);
        $principal->guest->update(['identification_type_id' => $dni->id]);

        $missing = (new TraPayloadMapper())->missingData($hotel, $stay, $occupancy, $principal->refresh(), TraSubmissionKind::Principal);

        $this->assertContains('tipo de documento', $missing);
        $this->assertContains('fecha de nacimiento', $missing);
        $this->assertContains('departamento/municipio de procedencia', $missing);
    }
}
