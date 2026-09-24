<?php

declare(strict_types=1);

namespace Tests\Feature\Middleware;

use App\Settings\GeneralSettings;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EnsureAppConfiguredTest extends TestCase
{
    #[Test]
    public function it_redirects_web_routes_when_a_required_setting_is_missing(): void
    {
        GeneralSettings::fake(['currency' => null]);

        $this->get(route('hotels.index'))
            ->assertRedirect(route('settings.edit'))
            ->assertSessionHas('error', trans('settings.messages.configuration_required'));
    }

    #[Test]
    public function it_allows_the_settings_page_when_a_required_setting_is_missing(): void
    {
        GeneralSettings::fake(['currency' => null]);

        $this->get(route('settings.edit'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Settings/Edit')
                ->where('missingSettings', ['currency']));
    }

    #[Test]
    public function it_allows_the_locale_to_change_when_a_required_setting_is_missing(): void
    {
        GeneralSettings::fake(['currency' => null]);

        $this->from(route('settings.edit'))
            ->post(route('locale.update'), ['locale' => 'en'])
            ->assertRedirect(route('settings.edit'))
            ->assertSessionHas('locale', 'en');
    }
}
