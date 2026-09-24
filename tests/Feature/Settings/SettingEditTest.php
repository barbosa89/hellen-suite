<?php

declare(strict_types=1);

namespace Tests\Feature\Settings;

use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SettingEditTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_and_updates_the_reference_currency(): void
    {
        GeneralSettings::fake(['currency' => null]);

        $this->get(route('settings.edit'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Settings/Edit')
                ->where('currency', null)
                ->where('missingSettings', ['currency'])
                ->has('currencies'));

        $this->put(route('settings.update'), ['currency' => 'COP'])
            ->assertRedirect(route('settings.edit'));

        $this->assertSame('COP', app(GeneralSettings::class)->currency);
    }
}
