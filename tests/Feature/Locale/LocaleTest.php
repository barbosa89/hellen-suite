<?php

declare(strict_types=1);

namespace Tests\Feature\Locale;

use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_persists_locale_in_general_settings(): void
    {
        $this->post(route('locale.update'), ['locale' => 'es'])
            ->assertRedirect();

        $this->assertSame('es', app(GeneralSettings::class)->language);
    }

    #[Test]
    public function it_reads_locale_from_settings_when_session_is_missing(): void
    {
        $settings = app(GeneralSettings::class);
        $settings->language = 'es';
        $settings->save();

        session()->flush();

        $this->get(route('settings.edit'))
            ->assertOk();

        $this->assertSame('es', app()->getLocale());
    }

    #[Test]
    public function it_prefers_settings_over_session_locale(): void
    {
        $settings = app(GeneralSettings::class);
        $settings->language = 'en';
        $settings->save();

        session(['locale' => 'es']);

        $this->get(route('settings.edit'))
            ->assertOk();

        $this->assertSame('en', app()->getLocale());
    }

    #[Test]
    public function it_updates_persisted_locale_when_changed(): void
    {
        $settings = app(GeneralSettings::class);
        $settings->language = 'en';
        $settings->save();

        $this->post(route('locale.update'), ['locale' => 'es'])
            ->assertRedirect();

        $this->assertSame('es', app(GeneralSettings::class)->language);
    }

    #[Test]
    public function it_uses_default_locale_when_no_setting_and_no_session(): void
    {
        GeneralSettings::fake(['language' => null]);

        session()->flush();

        $this->get(route('settings.edit'))
            ->assertOk();

        $this->assertSame(config('app.locale'), app()->getLocale());
    }
}
