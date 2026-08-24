<?php

declare(strict_types=1);

namespace Tests\Feature\Settings;

use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SettingUpdateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_rejects_an_unknown_currency_code(): void
    {
        $this->put(route('settings.update'), ['currency' => 'XXX'])
            ->assertSessionHasErrors('currency');
    }

    #[Test]
    public function it_allows_changing_the_reference_currency_without_conversion(): void
    {
        $this->put(route('settings.update'), ['currency' => 'COP']);
        $this->put(route('settings.update'), ['currency' => 'USD'])
            ->assertRedirect(route('settings.edit'));

        $this->assertSame('USD', app(GeneralSettings::class)->currency);
    }
}
