<?php

namespace Tests\Traits;

trait HasFlashMessages
{
    public function asssertFlashMessage(string $message, string $level = 'success'): void
    {
        $notification = session('flash_notification')->first();

        $this->assertEquals($message, $notification->message);
        $this->assertEquals($level, $notification->level);
        $this->assertEquals(false, $notification->important);
        $this->assertEquals(false, $notification->overlay);
    }
}
