<?php

declare(strict_types=1);

namespace Tests\Feature\Hotels;

use App\Constants\ComplianceScheme;
use App\Models\Hotel;
use App\Models\HotelComplianceProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HotelComplianceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stores_jurisdiction_and_a_compliance_profile_for_a_colombian_hotel(): void
    {
        $this->post(route('hotels.store'), [
            'business_name' => 'Hotel Andino',
            'tin' => '900123456',
            'country_code' => 'co',
            'timezone' => 'America/Bogota',
            'establishment_code' => '12345',
            'credential' => 'secret-credential',
            'compliance_enabled' => true,
        ])->assertRedirect(route('hotels.index'))
            ->assertSessionHasNoErrors();

        $hotel = Hotel::query()->where('tin', '900123456')->firstOrFail();

        $this->assertSame('CO', $hotel->country_code);
        $this->assertSame('America/Bogota', $hotel->timezone);
        $this->assertSame('co-tra', $hotel->complianceStrategy());

        $profile = $hotel->resolveCurrentComplianceProfile();

        $this->assertInstanceOf(HotelComplianceProfile::class, $profile);
        $this->assertSame('CO', $profile->jurisdiction);
        $this->assertSame(ComplianceScheme::Tra, $profile->scheme);
        $this->assertSame('12345', $profile->establishment_code);
        $this->assertTrue($profile->enabled);
        $this->assertTrue($profile->isConfigured());

        // The credential must be encrypted at rest and never stored in plain text.
        $this->assertNotContains('secret-credential', (array) $profile->getRawOriginal('credentials'));
        $this->assertSame('secret-credential', $profile->credentials['secret']);
    }

    #[Test]
    public function it_rejects_compliance_without_colombian_jurisdiction_code_and_credential(): void
    {
        $this->post(route('hotels.store'), [
            'business_name' => 'Hotel Español',
            'tin' => '900123457',
            'country_code' => 'ES',
            'compliance_enabled' => true,
            'establishment_code' => '12345',
            'credential' => 'secret-credential',
        ])->assertSessionHasErrors('compliance_enabled');

        $this->post(route('hotels.store'), [
            'business_name' => 'Hotel Andino',
            'tin' => '900123458',
            'country_code' => 'CO',
            'compliance_enabled' => true,
        ])->assertSessionHasErrors(['establishment_code', 'credential']);
    }

    #[Test]
    public function it_rejects_unknown_country_and_timezone(): void
    {
        $this->post(route('hotels.store'), [
            'business_name' => 'Hotel Andino',
            'tin' => '900123459',
            'country_code' => 'XX',
            'timezone' => 'Mars/Olympus',
        ])->assertSessionHasErrors(['country_code', 'timezone']);
    }

    #[Test]
    public function it_keeps_the_stored_credential_when_the_update_leaves_it_blank(): void
    {
        $hotel = Hotel::factory()->create([
            'country_code' => 'CO',
            'timezone' => 'America/Bogota',
        ]);
        HotelComplianceProfile::factory()->for($hotel)->create([
            'establishment_code' => '12345',
            'credentials' => ['secret' => 'original-secret'],
            'enabled' => true,
        ]);

        $this->patch(route('hotels.update', $hotel), [
            'business_name' => $hotel->business_name,
            'tin' => $hotel->tin,
            'country_code' => 'CO',
            'compliance_enabled' => true,
            'establishment_code' => '12345',
            'credential' => '',
        ])->assertSessionHasNoErrors();

        $this->assertSame(
            'original-secret',
            $hotel->resolveCurrentComplianceProfile()->refresh()->credentials['secret'],
        );
    }

    #[Test]
    public function it_exposes_compliance_status_without_leaking_credentials(): void
    {
        $hotel = Hotel::factory()->create(['country_code' => 'CO']);
        HotelComplianceProfile::factory()->for($hotel)->create(['enabled' => true]);

        $this->get(route('hotels.show', $hotel))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Hotels/Show')
                ->where('hotel.current_compliance_profile.configured', true)
                ->missing('hotel.current_compliance_profile.credentials'));
    }
}
