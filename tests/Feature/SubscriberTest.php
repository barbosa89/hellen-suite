<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use Spatie\Newsletter\Newsletter;

class SubscriberTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_can_subscribe_someone_as_pending()
    {
        $email = 'contacto@omarbarbosa.com';

        $mock = Mockery::mock(Newsletter::class);
        $mock->shouldReceive('isSubscribed')->once()->with($email)->andReturn(false);
        $mock->shouldReceive('subscribePending')->once()->with($email);

        $this->app->instance(Newsletter::class, $mock);

        $this->post('/subscribe', [
            'email' => $email
        ])->assertRedirect('/');

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('landing.subscribers.title'), $message->title);
        $this->assertEquals(trans('landing.subscribers.pending'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(true, $message->overlay);
    }

    public function test_it_check_the_user_is_already_subscribed()
    {
        $email = 'contacto@omarbarbosa.com';

        $mock = Mockery::mock(Newsletter::class);
        $mock->shouldReceive('isSubscribed')->once()->with($email)->andReturn(true);
        $mock->shouldNotReceive('subscribePending');

        $this->app->instance(Newsletter::class, $mock);

        $this->post('/subscribe', [
            'email' => $email
        ])->assertRedirect('/');

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('landing.subscribers.title'), $message->title);
        $this->assertEquals(trans('landing.subscribers.exists'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(true, $message->overlay);
    }

    public function test_user_can_unsubscribe()
    {
        $email = 'contacto@omarbarbosa.com';

        $mock = Mockery::mock(Newsletter::class);
        $mock->shouldReceive('isSubscribed')->once()->with($email)->andReturn(true);
        $mock->shouldReceive('unsubscribe')->once()->with($email);

        $this->app->instance(Newsletter::class, $mock);

        $this->get('/unsubscribe/' . $email)
            ->assertRedirect('/');

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('landing.subscribers.title'), $message->title);
        $this->assertEquals(trans('landing.subscribers.leaves'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(true, $message->overlay);
    }

    public function test_user_cannot_unsubscribe_because_is_not_subscribed()
    {
        $email = 'contacto@omarbarbosa.com';

        $mock = Mockery::mock(Newsletter::class);
        $mock->shouldReceive('isSubscribed')->once()->with($email)->andReturn(false);
        $mock->shouldNotReceive('unsubscribe');

        $this->app->instance(Newsletter::class, $mock);

        $this->get('/unsubscribe/' . $email)
            ->assertRedirect('/');

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('landing.subscribers.title'), $message->title);
        $this->assertEquals(trans('landing.subscribers.unknown'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(true, $message->overlay);
    }
}
