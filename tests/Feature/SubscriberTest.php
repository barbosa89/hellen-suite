<?php

namespace Tests\Feature;

use Spatie\Newsletter\Facades\Newsletter;
use Tests\TestCase;

class SubscriberTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('newsletter.driver_arguments.endpoint', '');
    }

    public function test_it_can_subscribe_someone_as_pending()
    {
        $email = 'contacto@omarbarbosa.com';

        Newsletter::shouldReceive('isSubscribed')->once()->with($email)->andReturn(false);
        Newsletter::shouldReceive('subscribePending')->once()->with($email);

        $this->post('/subscribe', [
            'email' => $email,
        ])->assertRedirect('/');

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('landing.subscribers.title'), $message->title);
        $this->assertEquals(trans('landing.subscribers.pending'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(true, $message->overlay);
    }

    public function test_it_check_the_guest_is_already_subscribed()
    {
        $email = 'contacto@omarbarbosa.com';

        Newsletter::shouldReceive('isSubscribed')->once()->with($email)->andReturn(true);
        Newsletter::shouldNotReceive('subscribePending');

        $this->post('/subscribe', [
            'email' => $email,
        ])->assertRedirect('/');

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('landing.subscribers.title'), $message->title);
        $this->assertEquals(trans('landing.subscribers.exists'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(true, $message->overlay);
    }

    public function test_guest_can_unsubscribe()
    {
        $email = 'contacto@omarbarbosa.com';

        Newsletter::shouldReceive('isSubscribed')->once()->with($email)->andReturn(true);
        Newsletter::shouldReceive('unsubscribe')->once()->with($email);

        $this->get('/unsubscribe/'.$email)
            ->assertRedirect('/');

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('landing.subscribers.title'), $message->title);
        $this->assertEquals(trans('landing.subscribers.leaves'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(true, $message->overlay);
    }

    public function test_guest_cannot_unsubscribe_because_is_not_subscribed()
    {
        $email = 'contacto@omarbarbosa.com';

        Newsletter::shouldReceive('isSubscribed')->once()->with($email)->andReturn(false);
        Newsletter::shouldNotReceive('unsubscribe');

        $this->get('/unsubscribe/'.$email)
            ->assertRedirect('/');

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('landing.subscribers.title'), $message->title);
        $this->assertEquals(trans('landing.subscribers.unknown'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(true, $message->overlay);
    }
}
