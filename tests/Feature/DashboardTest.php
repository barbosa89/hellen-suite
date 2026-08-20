<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    #[Test]
    public function dashboard_routes_redirect_to_hotel_index(): void
    {
        $this->get('/')
            ->assertRedirect(route('hotels.index'));

        $this->get(route('dashboard'))
            ->assertRedirect(route('hotels.index'));
    }
}
