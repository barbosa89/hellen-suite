<?php

namespace Tests\Feature;

use App\Mail\ContactMessage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use WithFaker;

    public function test_guest_can_send_a_message()
    {
        Mail::fake();

        $data = [
            'contact_name' => $this->faker->firstName(),
            'contact_lastname' => $this->faker->lastName,
            'contact_email' => 'contacto@omarbarbosa.com',
            'contact_phone' => '1231231230',
            'contact_message' => $this->faker->sentence(20),
        ];

        $this->post(route('message'), $data)
            ->assertRedirect('/');

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.great'), $message->title);
        $this->assertEquals(trans('email.sent'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(true, $message->overlay);

        Mail::assertSent(fn (ContactMessage $mail) => $mail->message->contact_email === $data['contact_email']);
    }
}
