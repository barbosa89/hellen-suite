<?php

namespace Tests\Feature;

use Tests\TestCase;
use InvalidArgumentException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HelperTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_get_columns_helper()
    {
        $usersTable = [
            "id",
            "name",
            "email",
            "password",
            "token",
            "status",
            "parent",
            "remember_token",
            "timezone",
            "email_verified_at",
            "created_at",
            "updated_at",
        ];

        $this->assertTrue(is_array(get_columns('users')));
        $this->assertEquals($usersTable, get_columns('users'));
    }

    public function test_get_columns_helper_as_dotted_style()
    {
        $usersTable = [
            "users.id",
            "users.name",
            "users.email",
            "users.password",
            "users.token",
            "users.status",
            "users.parent",
            "users.remember_token",
            "users.timezone",
            "users.email_verified_at",
            "users.created_at",
            "users.updated_at",
        ];

        $this->assertTrue(is_array(get_columns('users')));
        $this->assertEquals($usersTable, get_columns('users', true));
    }

    public function test_external_url_helper()
    {
        $url = 'http://domain.com/';
        $params = [
            'param' => 'value',
            'param2' => 'value2'
        ];

        $this->assertEquals($url . '?param=value&param2=value2', external_url($url, $params));
    }

    public function test_cents_to_float_helper()
    {
        $this->assertEquals(100.00, cents_to_float("10000"));
        $this->assertEquals(123.45, cents_to_float("12345"));
        $this->assertEquals(0.01, cents_to_float("1"));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function test_cents_to_float_helper_with_exception()
    {
        $this->expectException(InvalidArgumentException::class);

        cents_to_float("-10");
    }

    public function test_get_colors()
    {
        $colors = [
            [
                'bar' => 'rgba(255, 102, 102, 0.2)',
                'border' => 'rgba(255, 102, 102, 1)'
            ],
            [
                'bar' => 'rgba(5, 32, 74, 0.2)',
                'border' => 'rgba(5, 32, 74, 1)'
            ],
            [
                'bar' => 'rgba(117, 185, 190, 0.2)',
                'border' => 'rgba(117, 185, 190, 1)'
            ],
            [
                'bar' => 'rgba(245, 158, 62, 0.2)',
                'border' => 'rgba(245, 158, 62, 1)'
            ],
            [
                'bar' => 'rgba(255, 211, 218, 0.2)',
                'border' => 'rgba(255, 211, 218, 1)'
            ],
            [
                'bar' => 'rgba(35, 206, 107, 0.2)',
                'border' => 'rgba(35, 206, 107, 1)'
            ],
        ];

        $this->assertEquals($colors, get_colors());
    }
}
